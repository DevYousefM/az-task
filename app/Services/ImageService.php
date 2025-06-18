<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    private const STORAGE_DISK = 'local';
    private const PRODUCT_IMAGE_PATH = 'products';

    /**
     * Upload product image
     */
    public function uploadProductImage(UploadedFile $image): string
    {
        return $image->store(self::PRODUCT_IMAGE_PATH, self::STORAGE_DISK);
    }

    /**
     * Delete product image if exists
     */
    public function deleteProductImage(?string $imagePath): bool
    {
        if ($imagePath && Storage::disk(self::STORAGE_DISK)->exists($imagePath)) {
            return Storage::disk(self::STORAGE_DISK)->delete($imagePath);
        }
        return false;
    }

    /**
     * Update product image (delete old, upload new)
     */
    public function updateProductImage(UploadedFile $newImage, ?string $oldImagePath = null): string
    {
        // Delete old image if exists
        $this->deleteProductImage($oldImagePath);

        // Upload new image
        return $this->uploadProductImage($newImage);
    }
} 