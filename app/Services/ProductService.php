<?php

namespace App\Services;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ImageService $imageService,
        private CacheService $cacheService
    ) {}

    /**
     * Get paginated products
     */
    public function getPaginatedProducts(): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 6);

        return $this->productRepository->getPaginatedProducts($perPage, $page);
    }

    /**
     * Create a new product
     */
    public function createNewProduct(ProductRequest $request): object
    {
        $data = $request->validated();

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->imageService->uploadProductImage($data['image']);
        }

        return $this->productRepository->create($data);
    }

    /**
     * Get product by ID
     */
    public function getProductById(int $id): object
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }

    /**
     * Update a product
     */
    public function updateProduct(int $id, array $data): object
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $data['image'] = $this->imageService->updateProductImage(
                    $data['image'], 
                    $product->image
                );
            }
        }

        return $this->productRepository->update($id, $data);
    }

    /**
     * Delete a product
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);
        
        if ($product && $product->image) {
            $this->imageService->deleteProductImage($product->image);
        }

        return $this->productRepository->delete($id);
    }
}
