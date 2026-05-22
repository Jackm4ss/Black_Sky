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
                    ->description('Manage the login identity, role access, and active status.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->relationship(
                                'roles',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->where('name', 'user'),
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('User Management only manages public member accounts.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email verified at')
                            ->seconds(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Registration Attribution')
                    ->description('These fields feed the dashboard registration traffic and top country charts.')
                    ->schema([
                        Forms\Components\Select::make('registration_source')
                            ->label('Traffic source')
                            ->options(RegistrationSourceMeta::options())
                            ->searchable(),
                        Forms\Components\Select::make('registration_country_code')
                            ->label('Country')
                            ->options(CountryDataList::assignedCountryOptions())
                            ->searchable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Str::upper($state) : null),
                        Forms\Components\TextInput::make('registration_referrer')
                            ->label('Referrer')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('name')
                    ->label('Name')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => Auth::id() !== $record->id),
            ])
            ->actionsAlignment('center')
            ->actionsColumnLabel('Action')
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
}
