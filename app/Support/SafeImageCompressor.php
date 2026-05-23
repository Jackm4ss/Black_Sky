<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SafeImageCompressor
{
    public const DEFAULT_ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function storeUploaded(UploadedFile $upload, ImageCompressionOptions $options): string
    {
        if (! $upload->isValid()) {
            $this->fail($options, 'The '.$options->label.' upload did not complete.');
        }

        $sourcePath = $upload->getRealPath();

        if (! is_string($sourcePath) || ! is_file($sourcePath)) {
            $this->fail($options, 'The '.$options->label.' could not be read.');
        }

        $reportedMimeType = (string) $upload->getMimeType();

        if (! in_array($reportedMimeType, $options->allowedMimeTypes, true)) {
            $this->failUnsupported($options);
        }

        return $this->compressFromPath($sourcePath, $options);
    }

    public function storePublicDiskPath(string $path, ImageCompressionOptions $options): string
    {
        $path = $this->normalizePublicPath($path, $options->errorField);
        $disk = Storage::disk($options->disk);

        if (! $disk->exists($path)) {
            $this->fail($options, 'The '.$options->label.' upload could not be found. Please upload it again.');
        }

        $storedPath = $this->compressFromPath($disk->path($path), $options);

        if ($options->deleteSource) {
            $disk->delete($path);
        }

        return $storedPath;
    }

    public function deletePublicPath(?string $path, string $requiredPrefix): bool
    {
        if (blank($path)) {
            return false;
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['data:'])) {
            return false;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $urlPath = parse_url($path, PHP_URL_PATH);
            $path = is_string($urlPath) ? $urlPath : '';

            if (! str_contains($path, '/storage/')) {
                return false;
            }
        }

        if (Str::startsWith($path, ['/storage/'])) {
            $path = Str::after($path, '/storage/');
        }

        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            return false;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $requiredPrefix = trim($requiredPrefix, '/').'/';

        if (! Str::startsWith($path, $requiredPrefix)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    private function compressFromPath(string $sourcePath, ImageCompressionOptions $options): string
    {
        @ini_set('memory_limit', $options->memoryLimit);
        @set_time_limit($options->timeLimitSeconds);

        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            $this->fail($options, 'The '.$options->label.' file could not be read as an image.');
        }

        [$sourceWidth, $sourceHeight, $imageType] = $imageInfo;
        $detectedMimeType = (string) ($imageInfo['mime'] ?? '');
        $sourcePixels = (int) $sourceWidth * (int) $sourceHeight;

        if (
            $sourceWidth < 1 ||
            $sourceHeight < 1 ||
            $sourcePixels > $options->maxPixels ||
            ! in_array($detectedMimeType, $options->allowedMimeTypes, true)
        ) {
            $this->fail($options, 'Upload a valid JPG, PNG, WEBP, or GIF under the supported resolution limit.');
        }

        $source = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            default => false,
        };

        if (! $source) {
            $this->failUnsupported($options);
        }

        $scale = min(1, $options->maxEdge / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        $relativePath = $this->targetPath($options);
        $disk = Storage::disk($options->disk);
        $disk->makeDirectory(trim($options->directory, '/'));

        $stored = @imagewebp($target, $disk->path($relativePath), $options->quality);

        imagedestroy($source);
        imagedestroy($target);

        if (! $stored) {
            $this->fail($options, 'The '.$options->label.' could not be compressed. Please try another image.');
        }

        return $options->returnUrl ? $disk->url($relativePath) : $relativePath;
    }

    private function targetPath(ImageCompressionOptions $options): string
    {
        $safePrefix = Str::slug($options->filenamePrefix) ?: 'image';

        return trim($options->directory, '/').'/'.$safePrefix.'-'.Str::uuid().'.webp';
    }

    private function normalizePublicPath(string $path, string $errorField): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (
            $path === '' ||
            Str::startsWith($path, ['http://', 'https://', 'data:']) ||
            str_contains($path, '..') ||
            str_contains($path, "\0")
        ) {
            throw ValidationException::withMessages([
                $errorField => 'The uploaded image path is not valid.',
            ]);
        }

        return $path;
    }

    private function failUnsupported(ImageCompressionOptions $options): never
    {
        $this->fail($options, 'The '.$options->label.' format is not supported. Please upload JPG, PNG, WEBP, or GIF.');
    }

    private function fail(ImageCompressionOptions $options, string $message): never
    {
        throw ValidationException::withMessages([
            $options->errorField => $message,
        ]);
    }
}
