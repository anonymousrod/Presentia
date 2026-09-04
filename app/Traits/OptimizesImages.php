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
     * En cas de format non bitmap (SVG, ICO) ou d'erreur de décodage, sauvegarde le fichier original de manière sécurisée.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $path The directory path (e.g. 'settings', 'avatars')
     * @param int|null $maxWidth Maximum width of the image (default 1920)
     * @param int $quality Quality of the output WebP (default 80)
     * @return string The path relative to the public storage
     */
    public function optimizeAndStoreImage(UploadedFile $file, string $path, ?int $maxWidth = 1920, int $quality = 80): string
    {
        $fullDirectoryPath = storage_path('app/public/' . $path);
        if (!file_exists($fullDirectoryPath)) {
            mkdir($fullDirectoryPath, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $mimeType = $file->getMimeType() ?: '';

        // 1. Formats vectoriels ou spécifiques (SVG, ICO) qui ne doivent pas être convertis en bitmap/WebP
        if (in_array($extension, ['svg', 'ico', 'svgz']) || str_contains($mimeType, 'svg') || str_contains($mimeType, 'x-icon') || str_contains($mimeType, 'vnd.microsoft.icon')) {
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $file->move($fullDirectoryPath, $filename);
            return $path . '/' . $filename;
        }

        // 2. Traitement et optimisation WebP via Intervention Image
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath() ?: $file->getPathname());

            // Redimensionnement proportionnel si plus grand que maxWidth
            if ($maxWidth && $image->width() > $maxWidth) {
                $image->scale(width: $maxWidth);
            }

            $filename = uniqid() . '_' . time() . '.webp';
            $fullPath = $fullDirectoryPath . '/' . $filename;

            // Enregistrement en WebP
            $image->toWebp($quality)->save($fullPath);

            return $path . '/' . $filename;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("OptimizesImages: impossible d'optimiser l'image via Intervention ({$e->getMessage()}). Sauvegarde directe du fichier source.");

            // Fallback de sécurité : enregistrement direct du fichier d'origine sans plantage
            $fallbackExtension = $extension ?: 'jpg';
            $filename = uniqid() . '_' . time() . '.' . $fallbackExtension;
            $file->move($fullDirectoryPath, $filename);

            return $path . '/' . $filename;
        }
    }
}
