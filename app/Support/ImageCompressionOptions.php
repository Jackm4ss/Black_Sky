<?php

namespace App\Support;

final readonly class ImageCompressionOptions
{
    /**
     * @param  list<string>  $allowedMimeTypes
     */
    public function __construct(
        public string $directory,
        public string $filenamePrefix,
        public string $errorField,
        public string $label,
        public int $maxEdge,
        public int $quality = 84,
        public int $maxPixels = 50000000,
        public string $disk = 'public',
        public bool $returnUrl = false,
        public bool $deleteSource = false,
        public array $allowedMimeTypes = SafeImageCompressor::DEFAULT_ALLOWED_MIME_TYPES,
        public string $memoryLimit = '512M',
        public int $timeLimitSeconds = 90,
    ) {}
}
