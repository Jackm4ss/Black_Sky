<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureEmailVerificationMail();

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        Fortify::authenticateUsing(function (Request $request): ?User {
            $email = Str::lower((string) $request->input(Fortify::username()));
            $user = User::query()->where('email', $email)->first();

            if (! $user || ! $user->is_active) {
                return null;
            }

            if (! Hash::check((string) $request->input('password'), $user->password)) {
                return null;
            }

            $user->loadMissing('roles');

            if ($user->hasRole('admin') || ! $user->hasRole('user')) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('auth.failed'),
                ]);
            }

            if (! $user->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    Fortify::username() => 'Please verify your email before signing in.',
                ]);
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('authentication', function (Request $request) {
            $email = Str::lower((string) $request->input(Fortify::username()));
            $throttleKey = Str::transliterate($email.'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('verification', function (Request $request) {
            $user = $request->user();
            $throttleKey = $user
                ? 'verification|user:'.$user->getAuthIdentifier()
                : 'verification|ip:'.$request->ip();

            return Limit::perSecond(1, 35)
                ->by($throttleKey)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Please wait before requesting another verification email.',
                    ], 429, $headers);
                });
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }

    private function configureEmailVerificationMail(): void
    {
        VerifyEmail::createUrlUsing(function (User $notifiable): string {
            return URL::temporarySignedRoute(
                'api.verify-email',
                Carbon::now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );
        });

        VerifyEmail::toMailUsing(function (User $notifiable, string $verificationUrl): MailMessage {
            $data = [
                'appName' => (string) config('app.name', 'Black Sky'),
                'userName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'logoUrl' => asset('images/black-sky-logo.png'),
                'expiresInMinutes' => (int) Config::get('auth.verification.expire', 60),
                'supportEmail' => (string) config('mail.from.address', 'hello@blacksky.test'),
                'siteHost' => parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'blacksky.test',
            ];

            return (new MailMessage)
                ->subject('Verify your Black Sky account')
                ->view('emails.auth.verify-email', $data)
                ->text('emails.auth.verify-email-text', $data);
        });
    }
}
