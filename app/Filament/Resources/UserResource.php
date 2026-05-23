<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\CountryDataList;
use App\Support\RegistrationSourceMeta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'User Management';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereDoesntHave('roles', fn (Builder $query): Builder => $query->where('name', 'admin'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account')
                    ->description('Manage the member identity, account status, and registration details.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date of birth')
                            ->maxDate(now())
                            ->native(false),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'non_binary' => 'Non-binary',
                                'prefer_not_to_say' => 'Prefer not to say',
                            ])
                            ->placeholder('Select gender'),
                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\ViewField::make('registration_country_code')
                            ->label('Country')
                            ->id('bsa-user-country-code')
                            ->view('filament.forms.components.admin-country-dropdown')
                            ->live()
                            ->rules(['nullable', 'string', 'size:2'])
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Str::upper($state) : null),
                        Forms\Components\ViewField::make('phone')
                            ->label('Mobile number')
                            ->id('bsa-user-phone')
                            ->view('filament.forms.components.admin-phone-input')
                            ->viewData([
                                'countryInputId' => 'bsa-user-country-code',
                            ])
                            ->rules(['nullable', 'string', 'max:24'])
                            ->dehydrateStateUsing(fn (?string $state): ?string => self::mobileNumberForStorage($state)),
                        Forms\Components\Placeholder::make('saved_events')
                            ->label('Saved events')
                            ->content(fn (?User $record): HtmlString => self::savedEventsContent($record))
                            ->visible(fn (?User $record): bool => (bool) $record?->exists)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->maxLength(255),
                        Forms\Components\Select::make('is_active')
                            ->label('Active')
                            ->options([
                                '1' => 'Active',
                                '0' => 'Inactive',
                            ])
                            ->formatStateUsing(fn ($state): string => $state === false || (string) $state === '0' ? '0' : '1')
                            ->dehydrateStateUsing(fn (string | int | bool | null $state): bool => (string) $state === '1')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email verified at')
                            ->seconds(false),
                        Forms\Components\Select::make('registration_source')
                            ->label('Traffic source')
                            ->options(RegistrationSourceMeta::options())
                            ->searchable(),
                        Forms\Components\TextInput::make('registration_referrer')
                            ->label('Referrer')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('name')
                    ->label('Member')
                    ->view('filament.tables.columns.user-identity')
                    ->searchable(['name', 'email'])
                    ->sortable(),
                Tables\Columns\ViewColumn::make('registration_source')
                    ->label('Source')
                    ->view('filament.tables.columns.registration-source'),
                Tables\Columns\ViewColumn::make('registration_country_code')
                    ->label('Country')
                    ->view('filament.tables.columns.registration-country'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('registration_country_code')
                    ->label('Country')
                    ->options(fn (): array => self::registrationCountryFilterOptions())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('registration_source')
                    ->label('Traffic source')
                    ->options(RegistrationSourceMeta::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (User $record): bool => Auth::id() !== $record->id),
            ])
            ->actionsAlignment('center')
            ->actionsColumnLabel('Actions')
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function registrationCountryFilterOptions(): array
    {
        $codes = self::getEloquentQuery()
            ->whereNotNull('registration_country_code')
            ->distinct()
            ->pluck('registration_country_code')
            ->all();

        return CountryDataList::assignedCountryOptionsForCodes($codes);
    }

    private static function mobileNumberForStorage(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?: '';

        if ($digits === '') {
            return null;
        }

        if (Str::startsWith($value, '+')) {
            return '+' . $digits;
        }

        return $digits;
    }

    private static function savedEventsContent(?User $record): HtmlString
    {
        if (! $record?->exists) {
            return new HtmlString('<span class="bsa-form-muted">Saved events appear after this account is created.</span>');
        }

        $total = $record->bookmarks()->count();

        if ($total === 0) {
            return new HtmlString('<span class="bsa-form-muted">No saved events yet.</span>');
        }

        $bookmarks = $record->bookmarks()
            ->with(['event:id,title,start_date,venue,city,country_code'])
            ->latest()
            ->limit(6)
            ->get();

        $items = $bookmarks
            ->map(function ($bookmark): string {
                $event = $bookmark->event;

                if (! $event) {
                    return '';
                }

                $date = $event->start_date?->format('M d, Y');
                $location = collect([$event->venue, $event->city, $event->country_code])
                    ->filter()
                    ->implode(', ');
                $meta = collect([$date, $location])
                    ->filter()
                    ->implode(' · ');

                return '<li><strong>' . e($event->title) . '</strong>' .
                    ($meta !== '' ? '<span>' . e($meta) . '</span>' : '') .
                    '</li>';
            })
            ->filter()
            ->implode('');

        $remaining = max(0, $total - $bookmarks->count());

        return new HtmlString(
            '<div class="bsa-user-saved-events">' .
                '<div><strong>' . number_format($total) . '</strong><span>Total saved events</span></div>' .
                '<ul>' . $items . '</ul>' .
                ($remaining > 0 ? '<em>+' . number_format($remaining) . ' more saved events</em>' : '') .
            '</div>'
        );
    }
}
