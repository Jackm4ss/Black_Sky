<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioWorkResource\Pages;
use App\Models\PortfolioWork;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PortfolioWorkResource extends Resource
{
    protected static ?string $model = PortfolioWork::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Portfolio';

    protected static ?string $modelLabel = 'Portfolio Work';

    protected static ?string $pluralModelLabel = 'Portfolio';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'portfolio';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project')
                    ->description('Manage public portfolio items shown on the landing page and detail page.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get): void {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-chevron-down')
                            ->options(fn (): array => collect([
                                'Arena Concert',
                                'Arena Tour',
                                'Concert Production',
                                'Music Festival',
                                'Media Production',
                                'Brand Partnership',
                            ])
                                ->merge(PortfolioWork::query()->select('category')->distinct()->pluck('category'))
                                ->filter()
                                ->unique()
                                ->mapWithKeys(fn (string $category): array => [$category => $category])
                                ->all())
                            ->searchable(),
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('role')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('attendance')
                            ->maxLength(120),
                        Forms\Components\ColorPicker::make('accent_color')
                            ->label('Accent color')
                            ->required()
                            ->default('#f97316')
                            ->helperText('Used for portfolio card glow and detail page highlights.')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\Placeholder::make('current_featured_image')
                            ->label('Current featured image')
                            ->content(fn (?PortfolioWork $record): HtmlString|string => filled($record?->featured_image_url)
                                ? new HtmlString('<a class="bsa-current-media-link" href="' . e($record->featured_image_url) . '" target="_blank" rel="noopener">View current image</a>')
                                : 'No featured image uploaded yet.')
                            ->visible(fn (?PortfolioWork $record): bool => filled($record?->featured_image))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('featured_image_upload')
                            ->label('Featured image')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1600')
                            ->imageResizeTargetHeight('900')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->disk('public')
                            ->directory('portfolio')
                            ->visibility('public')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Upload the main project artwork shown on the landing page and detail page.')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('current_gallery_images')
                            ->label('Current gallery')
                            ->content(fn (?PortfolioWork $record): string => filled($record?->gallery_image_urls)
                                ? count($record->gallery_image_urls) . ' image(s) currently published. Uploading new gallery images replaces them.'
                                : 'No gallery images uploaded yet.')
                            ->visible(fn (?PortfolioWork $record): bool => filled($record?->gallery_images))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('gallery_image_uploads')
                            ->label('Gallery images')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1400')
                            ->imageResizeTargetHeight('900')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->disk('public')
                            ->directory('portfolio/gallery')
                            ->visibility('public')
                            ->helperText('Upload supporting project photos. Use at least three for a stronger detail page.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-chevron-down')
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->seconds(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image_url')
                    ->label('Image')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived' => 'gray',
                        default => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn (): array => PortfolioWork::query()
                        ->select('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->all()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data, ?PortfolioWork $record = null): array
    {
        if (filled($data['featured_image_upload'] ?? null)) {
            $data['featured_image'] = $data['featured_image_upload'];
        }

        unset($data['featured_image_upload']);

        if (array_key_exists('gallery_image_uploads', $data)) {
            $galleryImages = array_values(array_filter((array) $data['gallery_image_uploads']));

            if ($galleryImages !== []) {
                $data['gallery_images'] = $galleryImages;
            }
        }

        unset($data['gallery_image_uploads']);

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (($data['status'] ?? null) === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? $record?->featured_image;

        if (filled($featuredImage)) {
            $data['og_image'] = $featuredImage;
        }

        if (filled($data['slug'] ?? null)) {
            $data['canonical_url'] = url('/portfolio/' . $data['slug']);
        }

        if (filled($data['title'] ?? null)) {
            $data['meta_title'] = Str::limit((string) $data['title'] . ' | Black Sky Portfolio', 60, '');
        }

        $seoDescription = trim((string) ($data['excerpt'] ?? strip_tags((string) ($data['description'] ?? ''))));

        if ($seoDescription !== '') {
            $data['meta_description'] = Str::limit($seoDescription, 160, '');
        }

        $keywords = collect([
            $data['title'] ?? null,
            $data['category'] ?? null,
            $data['location'] ?? null,
            'Black Sky portfolio',
        ])
            ->filter(fn (mixed $value): bool => filled($value))
            ->map(fn (mixed $value): string => trim((string) $value))
            ->unique()
            ->implode(', ');

        if ($keywords !== '') {
            $data['meta_keywords'] = Str::limit($keywords, 255, '');
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolioWorks::route('/'),
            'create' => Pages\CreatePortfolioWork::route('/create'),
            'edit' => Pages\EditPortfolioWork::route('/{record}/edit'),
        ];
    }
}
