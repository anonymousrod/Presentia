<?php

namespace App\Traits;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait OptimizesImages
{
    /**
     * Resize, compress and save an uploaded image as WebP.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $path The directory path (e.g. 'settings', 'avatars')
     * @param int|null $maxWidth Maximum width of the image (default 1920)
     * @param int $quality Quality of the output WebP (default 80)
     * @return string The path relative to the public storage
     */
    public function optimizeAndStoreImage(UploadedFile $file, string $path, ?int $maxWidth = 1920, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($file->getRealPath());

        // Resize if it's wider than max width, keeping aspect ratio
        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        $filename = uniqid() . '_' . time() . '.webp';
        $fullDirectoryPath = storage_path('app/public/' . $path);

        if (!file_exists($fullDirectoryPath)) {
            mkdir($fullDirectoryPath, 0755, true);
        }

        $fullPath = $fullDirectoryPath . '/' . $filename;

        // Save as WebP
        $image->toWebp($quality)->save($fullPath);

        return $path . '/' . $filename;
    }
}
