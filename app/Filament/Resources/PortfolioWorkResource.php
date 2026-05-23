<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioWorkResource\Pages;
use App\Models\PortfolioWork;
use App\Support\ImageCompressionOptions;
use App\Support\SafeImageCompressor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PortfolioWorkResource extends Resource
{
    private const FEATURED_IMAGE_MAX_EDGE = 1600;

    private const FEATURED_IMAGE_QUALITY = 82;

    private const FEATURED_IMAGE_MAX_PIXELS = 50000000;

    private const GALLERY_IMAGE_MAX_EDGE = 1400;

    private const GALLERY_IMAGE_QUALITY = 84;

    private const GALLERY_IMAGE_MAX_PIXELS = 50000000;

    private const IMAGE_UPLOAD_MAX_KB = 102400;

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
                    ->description('Add the project story shown on the website.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->placeholder('Neon Pulse Arena Concert')
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-chevron-down')
                            ->placeholder('Arena Concert')
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
                            ->placeholder('2026')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->placeholder('Kuala Lumpur, Malaysia')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('role')
                            ->placeholder('Full Production')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('attendance')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->placeholder('18000')
                            ->maxLength(120),
                        Forms\Components\ColorPicker::make('accent_color')
                            ->label('Accent color')
                            ->required()
                            ->default('#f97316')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->required()
                            ->placeholder('A sold-out arena concert with full production, fan operations, and content capture.')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->placeholder('Describe the project, Black Sky role, and the final result.')
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
                                ? new HtmlString('<a class="bsa-current-media-link" href="'.e($record->featured_image_url).'" target="_blank" rel="noopener">View current image</a>')
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
                            ->maxSize(self::IMAGE_UPLOAD_MAX_KB)
                            ->disk('public')
                            ->directory('portfolio')
                            ->visibility('public')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->placeholder('Drop the project image here or browse')
                            ->helperText('Choose the main image shown with this project. The preview appears when the image is ready.')
                            ->uploadingMessage('Preparing image...')
                            ->uploadButtonPosition('right bottom')
                            ->uploadProgressIndicatorPosition('right bottom')
                            ->removeUploadedFileButtonPosition('left bottom')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('current_gallery_images')
                            ->label('Current gallery')
                            ->content(fn (?PortfolioWork $record): string => filled($record?->gallery_image_urls)
                                ? count($record->gallery_image_urls).' gallery image(s) published. Choosing new images will refresh the gallery.'
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
                            ->maxSize(self::IMAGE_UPLOAD_MAX_KB)
                            ->disk('public')
                            ->directory('portfolio/gallery')
                            ->visibility('public')
                            ->placeholder('Drop project photos here or browse')
                            ->helperText('Add optional project photos for the detail page. The preview appears when images are ready.')
                            ->uploadingMessage('Preparing images...')
                            ->uploadButtonPosition('right bottom')
                            ->uploadProgressIndicatorPosition('right bottom')
                            ->removeUploadedFileButtonPosition('left bottom')
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
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->seconds(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('title')
                    ->label('Project')
                    ->view('filament.tables.columns.portfolio-work')
                    ->searchable(['title', 'excerpt', 'location'])
                    ->sortable()
                    ->extraAttributes(['class' => 'bsa-resource-product-cell']),
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
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->actionsAlignment('center')
            ->actionsColumnLabel('Actions')
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data, ?PortfolioWork $record = null): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = filled($record?->slug)
                ? $record->slug
                : self::makeUniqueSlug((string) ($data['title'] ?? 'portfolio-work'), $record);
        } else {
            $data['slug'] = self::makeUniqueSlug((string) $data['slug'], $record);
        }

        if (array_key_exists('attendance', $data)) {
            $attendance = preg_replace('/\D+/', '', (string) ($data['attendance'] ?? ''));
            $data['attendance'] = $attendance !== '' ? $attendance : null;
        }

        if (filled($data['featured_image_upload'] ?? null)) {
            $data['featured_image'] = app(SafeImageCompressor::class)->storePublicDiskPath(
                (string) $data['featured_image_upload'],
                new ImageCompressionOptions(
                    directory: 'portfolio',
                    filenamePrefix: (string) $data['slug'],
                    errorField: 'featured_image_upload',
                    label: 'featured image',
                    maxEdge: self::FEATURED_IMAGE_MAX_EDGE,
                    quality: self::FEATURED_IMAGE_QUALITY,
                    maxPixels: self::FEATURED_IMAGE_MAX_PIXELS,
                    deleteSource: true,
                    timeLimitSeconds: 120,
                ),
            );

            self::deletePublicImage($record?->featured_image, 'portfolio/');
        }

        unset($data['featured_image_upload']);

        if (array_key_exists('gallery_image_uploads', $data)) {
            $galleryImages = array_values(array_filter((array) $data['gallery_image_uploads']));

            if ($galleryImages !== []) {
                $data['gallery_images'] = collect($galleryImages)
                    ->map(fn (string $path): string => app(SafeImageCompressor::class)->storePublicDiskPath(
                        $path,
                        new ImageCompressionOptions(
                            directory: 'portfolio/gallery',
                            filenamePrefix: (string) $data['slug'],
                            errorField: 'gallery_image_uploads',
                            label: 'gallery image',
                            maxEdge: self::GALLERY_IMAGE_MAX_EDGE,
                            quality: self::GALLERY_IMAGE_QUALITY,
                            maxPixels: self::GALLERY_IMAGE_MAX_PIXELS,
                            deleteSource: true,
                            timeLimitSeconds: 120,
                        ),
                    ))
                    ->all();

                collect((array) $record?->gallery_images)
                    ->each(fn (mixed $path): bool => self::deletePublicImage(is_string($path) ? $path : null, 'portfolio/gallery/'));
            }
        }

        unset($data['gallery_image_uploads']);

        if (($data['status'] ?? null) === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? $record?->featured_image;

        if (filled($featuredImage)) {
            $data['og_image'] = $featuredImage;
        }

        if (filled($data['slug'] ?? null)) {
            $data['canonical_url'] = url('/portfolio/'.$data['slug']);
        }

        if (filled($data['title'] ?? null)) {
            $data['meta_title'] = Str::limit((string) $data['title'].' | Black Sky Portfolio', 60, '');
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

    private static function deletePublicImage(?string $path, string $requiredPrefix): bool
    {
        return app(SafeImageCompressor::class)->deletePublicPath($path, $requiredPrefix);
    }

    private static function makeUniqueSlug(string $value, ?PortfolioWork $record = null): string
    {
        $baseSlug = Str::slug($value) ?: 'portfolio-work';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            PortfolioWork::query()
                ->where('slug', $slug)
                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
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
