<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\BlogPost;
use App\Support\ImageCompressionOptions;
use App\Support\SafeImageCompressor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    private const FEATURED_IMAGE_MAX_EDGE = 1600;

    private const FEATURED_IMAGE_QUALITY = 84;

    private const FEATURED_IMAGE_MAX_PIXELS = 50000000;

    private const FEATURED_IMAGE_UPLOAD_MAX_KB = 102400;

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'News';

    protected static ?string $modelLabel = 'News Article';

    protected static ?string $pluralModelLabel = 'News';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'news';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Article')
                    ->description('Create the story that appears on the website.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
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

                Forms\Components\Section::make('Category & Author')
                    ->schema([
                        Forms\Components\Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\Textarea::make('bio')->rows(3)->columnSpanFull(),
                                Forms\Components\TextInput::make('photo')->label('Photo URL')->url()->maxLength(2048),
                                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                                Forms\Components\Toggle::make('is_active')->default(true),
                            ]),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                                Forms\Components\Toggle::make('is_active')->default(true),
                            ]),
                        Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                                Forms\Components\Toggle::make('is_active')->default(true),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Placeholder::make('current_featured_image')
                            ->label('Current featured image')
                            ->content(fn (?BlogPost $record): HtmlString|string => filled($record?->featured_image_url)
                                ? new HtmlString('<a class="bsa-current-media-link" href="'.e($record->featured_image_url).'" target="_blank" rel="noopener">View current image</a>')
                                : 'No featured image uploaded yet.')
                            ->visible(fn (?BlogPost $record): bool => filled($record))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('featured_image_upload')
                            ->label('Featured image')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1600')
                            ->imageResizeTargetHeight('900')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(self::FEATURED_IMAGE_UPLOAD_MAX_KB)
                            ->disk('public')
                            ->directory('news')
                            ->visibility('public')
                            ->placeholder('Drop the article image here or browse')
                            ->helperText('Choose the main image shown with this article. The preview appears when the image is ready.')
                            ->uploadingMessage('Preparing image...')
                            ->uploadButtonPosition('right bottom')
                            ->uploadProgressIndicatorPosition('right bottom')
                            ->removeUploadedFileButtonPosition('left bottom')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->native(false)
                            ->suffixIcon('heroicon-m-calendar', isInline: true)
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->native(false)
                            ->suffixIcon('heroicon-m-calendar', isInline: true)
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
                    ->label('Article')
                    ->view('filament.tables.columns.news-article')
                    ->searchable(['title', 'excerpt'])
                    ->sortable()
                    ->extraAttributes(['class' => 'bsa-resource-product-cell']),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'scheduled' => 'warning',
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
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->actionsAlignment('center')
            ->actionsColumnLabel('Actions')
            ->defaultSort(fn (Builder $query): Builder => $query->orderByDesc('created_at')->orderByDesc('id'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data, ?BlogPost $record = null): array
    {
        if (filled($data['featured_image_upload'] ?? null)) {
            $data['featured_image'] = $data['featured_image_upload'];
        }

        unset($data['featured_image_upload']);

        if (filled($data['title'] ?? null)) {
            $data['slug'] = filled($record?->slug)
                ? $record->slug
                : self::makeUniqueSlug((string) $data['title'], $record);
        }

        if (filled($data['featured_image'] ?? null)) {
            $data['featured_image'] = app(SafeImageCompressor::class)->storePublicDiskPath(
                (string) $data['featured_image'],
                new ImageCompressionOptions(
                    directory: 'news',
                    filenamePrefix: (string) ($data['slug'] ?? 'news-article'),
                    errorField: 'featured_image_upload',
                    label: 'featured image',
                    maxEdge: self::FEATURED_IMAGE_MAX_EDGE,
                    quality: self::FEATURED_IMAGE_QUALITY,
                    maxPixels: self::FEATURED_IMAGE_MAX_PIXELS,
                    deleteSource: true,
                    timeLimitSeconds: 120,
                ),
            );

            self::deletePublicImage($record?->featured_image, 'news/');
        }

        if (($data['status'] ?? null) === 'published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        if (filled($data['slug'] ?? null)) {
            $data['canonical_url'] = url('/news/'.$data['slug']);
        }

        if (filled($data['title'] ?? null)) {
            $data['meta_title'] = Str::limit((string) $data['title'].' | Black Sky News', 60, '');
        }

        $descriptionSource = trim((string) ($data['excerpt'] ?? strip_tags((string) ($data['content'] ?? ''))));

        if ($descriptionSource !== '') {
            $data['meta_description'] = Str::limit($descriptionSource, 160, '');
        }

        $data['meta_keywords'] = self::defaultMetaKeywords(
            (string) ($data['title'] ?? ''),
            (string) ($data['excerpt'] ?? '')
        );

        $data['og_image'] = filled($data['featured_image'] ?? null)
            ? $data['featured_image']
            : $record?->featured_image;

        return $data;
    }

    private static function defaultMetaKeywords(string $title, string $excerpt): string
    {
        $keywords = collect(preg_split('/[^A-Za-z0-9]+/', $title.' '.$excerpt) ?: [])
            ->map(fn (string $word): string => Str::lower($word))
            ->filter(fn (string $word): bool => Str::length($word) >= 4)
            ->reject(fn (string $word): bool => in_array($word, ['black', 'news', 'sky'], true))
            ->unique()
            ->take(10)
            ->values()
            ->all();

        $keywords = collect(array_merge(['Black Sky', 'news'], $keywords))
            ->unique()
            ->values()
            ->all();

        return Str::limit(implode(', ', $keywords), 255, '');
    }

    private static function makeUniqueSlug(string $title, ?BlogPost $record = null): string
    {
        $baseSlug = Str::slug($title) ?: 'news-article';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            BlogPost::query()
                ->where('slug', $slug)
                ->when($record, fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function deletePublicImage(?string $path, string $requiredPrefix): bool
    {
        return app(SafeImageCompressor::class)->deletePublicPath($path, $requiredPrefix);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
