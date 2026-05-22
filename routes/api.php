<?php

use App\Http\Controllers\Api\Auth\AuthenticatedUserController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\PublicBlogPostController;
use App\Http\Controllers\Api\V1\PublicEventController;
use App\Http\Controllers\Api\V1\PublicPortfolioController;
use App\Http\Controllers\Api\V1\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/health', HealthController::class)->name('api.health');

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])
    ->middleware('web')
    ->name('api.sanctum.csrf-cookie');

Route::middleware('auth:sanctum')->get('/user', [AuthenticatedUserController::class, 'show'])
    ->name('api.user.show');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('api.verify-email');

Route::prefix('v1')->group(function () {
    Route::get('/events', [PublicEventController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.v1.events.index');

    Route::get('/events/{slug}', [PublicEventController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('api.v1.events.show');

    Route::get('/portfolio', [PublicPortfolioController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.v1.portfolio.index');

    Route::get('/portfolio/{slug}', [PublicPortfolioController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('api.v1.portfolio.show');

    Route::get('/news', [PublicBlogPostController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.v1.news.index');

    Route::get('/news/{slug}', [PublicBlogPostController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('api.v1.news.show');

    Route::get('/blog', [PublicBlogPostController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.v1.blog.index');

    Route::get('/blog/{slug}', [PublicBlogPostController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('api.v1.blog.show');
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', LogoutController::class)
        ->name('api.v1.logout');

    Route::middleware('verified')->group(function () {
        Route::get('/me/dashboard', [UserDashboardController::class, 'show'])
            ->middleware('throttle:120,1')
            ->name('api.v1.me.dashboard');

        Route::match(['patch', 'post'], '/me/account', [UserDashboardController::class, 'updateAccount'])
            ->middleware('throttle:60,1')
            ->name('api.v1.me.account.update');

        Route::delete('/me/account', [UserDashboardController::class, 'destroyAccount'])
            ->middleware('throttle:10,1')
            ->name('api.v1.me.account.destroy');

        Route::patch('/me/password', [UserDashboardController::class, 'updatePassword'])
            ->middleware('throttle:30,1')
            ->name('api.v1.me.password.update');

        Route::post('/me/bookmarks/{event}', [UserDashboardController::class, 'storeBookmark'])
            ->middleware('throttle:60,1')
            ->name('api.v1.me.bookmarks.store');

        Route::delete('/me/bookmarks/{event}', [UserDashboardController::class, 'destroyBookmark'])
            ->middleware('throttle:60,1')
            ->name('api.v1.me.bookmarks.destroy');
    });
});
