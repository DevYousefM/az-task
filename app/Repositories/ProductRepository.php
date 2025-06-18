<?php

namespace App\Repositories;

use App\Exceptions\ProductNotFoundException;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Get paginated products
     */
    public function getPaginatedProducts(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = "products_page_{$page}_per_page_{$perPage}";
        
        $this->cacheService->rememberCacheKey($cacheKey);
        $this->cacheService->rememberCacheKey('products_total');

        return $this->cacheService->remember($cacheKey, function () use ($perPage, $page) {
            return Product::orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Find a product by ID
     */
    public function find(int $id): ?object
    {
        $cacheKey = "product_{$id}";
        $this->cacheService->rememberCacheKey($cacheKey);

        return $this->cacheService->remember($cacheKey, function () use ($id) {
            return Product::find($id);
        });
    }

    /**
     * Create a new product
     */
    public function create(array $data): object
    {
        $product = Product::create($data);
        $this->cacheService->clearProductCache();
        
        return $product;
    }

    /**
     * Update a product
     */
    public function update(int $id, array $data): ?object
    {
        $product = Product::find($id);
        
        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        $product->update($data);
        $this->cacheService->forget("product_{$id}");
        $this->cacheService->clearProductCache();

        return $product->fresh();
    }

    /**
     * Delete a product
     */
    public function delete(int $id): bool
    {
        $product = Product::find($id);
        
        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        $deleted = $product->delete();
        
        if ($deleted) {
            $this->cacheService->forget("product_{$id}");
            $this->cacheService->clearProductCache();
        }

        return $deleted;
    }
}
