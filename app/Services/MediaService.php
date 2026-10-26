<?php

namespace App\Services;

use App\Support\ThreeDModelValidator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use RuntimeException;

class MediaService
{
    private const OPTIMIZED_WIDTHS = [480, 960, 1920];

    public function url(?string $path): ?string
    {
        return media_url($path);
    }

    public function storeImage(UploadedFile $file, string $directory): string
    {
        return $this->writeResponsiveImages($file->getPathname(), $directory);
    }

    public function storeImageFromPath(string $sourcePath, string $directory): string
    {
        if (! is_file($sourcePath) || ! is_readable($sourcePath)) {
            throw new RuntimeException('Image source file could not be read.');
        }

        return $this->writeResponsiveImages($sourcePath, $directory);
    }

    private function writeResponsiveImages(string $sourcePath, string $directory): string
    {
        $directory = trim($directory, '/');
        $disk = $this->disk();
        $name = Str::random(40);
        $manager = $this->imageManager();

        foreach (self::OPTIMIZED_WIDTHS as $width) {
            $image = $manager->decodePath($sourcePath)
                ->orient()
                ->scaleDown(width: $width);

            $encoded = $image->encodeUsingFormat(Format::WEBP, quality: 82);

            $path = $directory.'/'.$name.($width === 1920 ? '' : '-'.$width).'.webp';
            $disk->put($path, (string) $encoded);
        }

        return $directory.'/'.$name.'.webp';
    }

    public function storeRawImage(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->extension() ?: $file->guessExtension() ?: '');

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new RuntimeException('Unsupported image type.');
        }

        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

        $this->disk()->put($path, $file->get());

        return $path;
    }

    public function storeModel(UploadedFile $file, string $directory = 'models'): string
    {
        $extension = strtolower($file->extension() ?: '');

        if (
            ! in_array($extension, ['glb', 'gltf'], true)
            || ! ThreeDModelValidator::isValid((string) $file->getRealPath(), $extension)
        ) {
            throw new RuntimeException('Unsupported or invalid 3D model file.');
        }

        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

        $this->disk()->put($path, $file->get());

        return $path;
    }

    public function delete(?string $path): void
    {
        if (blank($path) || str_contains($path, '..')) {
            return;
        }

        $disk = $this->disk();
        $base = pathinfo($path, PATHINFO_FILENAME);
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $directory = $directory === '.' ? '' : $directory.'/';

        $disk->delete($path);

        foreach (self::OPTIMIZED_WIDTHS as $width) {
            if ($width === 1920) {
                continue;
            }

            $disk->delete($directory.$base.'-'.$width.'.webp');
        }
    }

    private function disk(): Filesystem
    {
        return Storage::disk(config('store.media_disk', 'public'));
    }

    private function imageManager(): ImageManager
    {
        return ImageManager::usingDriver(Driver::class);
    }
}
