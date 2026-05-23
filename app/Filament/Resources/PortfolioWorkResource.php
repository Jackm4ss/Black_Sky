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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PortfolioWorkResource extends Resource
{
    private const FEATURED_IMAGE_MAX_EDGE = 1600;

    private const FEATURED_IMAGE_QUALITY = 82;

    private const GALLERY_IMAGE_MAX_EDGE = 1400;

    private const GALLERY_IMAGE_QUALITY = 84;

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
                    ->description('Add project details for the public portfolio.')
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
            $data['featured_image'] = self::compressPublicImage(
                path: (string) $data['featured_image_upload'],
                slug: (string) $data['slug'],
                directory: 'portfolio',
                maxEdge: self::FEATURED_IMAGE_MAX_EDGE,
                quality: self::FEATURED_IMAGE_QUALITY,
                field: 'featured_image_upload',
                label: 'featured image',
            );

            self::deletePublicImage($record?->featured_image, 'portfolio/');
        }

        unset($data['featured_image_upload']);

        if (array_key_exists('gallery_image_uploads', $data)) {
            $galleryImages = array_values(array_filter((array) $data['gallery_image_uploads']));

            if ($galleryImages !== []) {
                $data['gallery_images'] = collect($galleryImages)
                    ->map(fn (string $path): string => self::compressPublicImage(
                        path: $path,
                        slug: (string) $data['slug'],
                        directory: 'portfolio/gallery',
                        maxEdge: self::GALLERY_IMAGE_MAX_EDGE,
                        quality: self::GALLERY_IMAGE_QUALITY,
                        field: 'gallery_image_uploads',
                        label: 'gallery image',
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

    private static function compressPublicImage(
        string $path,
        string $slug,
        string $directory,
        int $maxEdge,
        int $quality,
        string $field,
        string $label,
    ): string {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(120);

        $path = ltrim($path, '/');
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            throw ValidationException::withMessages([
                $field => 'The ' . $label . ' upload could not be found. Please upload it again.',
            ]);
        }

        $sourcePath = $disk->path($path);
        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                $field => 'The ' . $label . ' file could not be read as an image.',
            ]);
        }

        [$sourceWidth, $sourceHeight, $imageType] = $imageInfo;
        $source = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            default => false,
        };

        if (! $source) {
            throw ValidationException::withMessages([
                $field => 'The ' . $label . ' format is not supported. Please upload JPG, PNG, WEBP, or GIF.',
            ]);
        }

        $scale = min(1, $maxEdge / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        $safeSlug = Str::slug($slug) ?: 'portfolio-work';
        $relativePath = trim($directory, '/') . '/' . $safeSlug . '-' . Str::uuid() . '.webp';

        $disk->makeDirectory(trim($directory, '/'));

        $stored = @imagewebp($target, $disk->path($relativePath), $quality);

        imagedestroy($source);
        imagedestroy($target);

        if (! $stored) {
            throw ValidationException::withMessages([
                $field => 'The ' . $label . ' could not be compressed. Please try another image.',
            ]);
        }

        self::deletePublicImage($path, trim($directory, '/') . '/');

        return $relativePath;
    }

    private static function deletePublicImage(?string $path, string $requiredPrefix): bool
    {
        if (blank($path)) {
            return false;
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['/storage/'])) {
            $path = Str::after($path, '/storage/');
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:']) || ! Str::startsWith($path, $requiredPrefix)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
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
            $slug = $baseSlug . '-' . $suffix;
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
