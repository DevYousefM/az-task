<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected ProductRepository $productRepository;
    protected string $cacheKeyList = 'products:cache_keys';
    public function __construct(ProductRepository $productRepository,)
    {
        $this->productRepository = $productRepository;
    }

    public function getPaginatedProducts(): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 6);

        $this->rememberProductCacheKey('products_total');
        $this->rememberProductCacheKey("products_page_{$page}_per_page_{$perPage}");

        $result = $this->productRepository->getPaginatedProducts($perPage, $page);

        return $result;
    }
    public function createNewProduct(ProductRequest $request)
    {
        $data = $request->validated();

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        $product = $this->productRepository->create($data);

        $this->clearProductsCache();

        return $product;
    }
    protected function clearProductsCache()
    {
        $cacheKeys = Cache::get($this->cacheKeyList, []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget($this->cacheKeyList);
    }
    public function rememberProductCacheKey(string $key): void
    {
        $keys = Cache::get($this->cacheKeyList, []);
        $keys[] = $key;
        $keys = array_unique($keys);
        Cache::put($this->cacheKeyList, $keys, env('CACHE_TTL', 60));
    }
    public function getProductById($id)
    {
        $product = $this->productRepository->find($id);
        $this->rememberProductCacheKey("product_{$id}");

        return $product;
    }
    public function updateProduct(int $id, array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $product = $this->productRepository->find($id);
            if ($product && $product->image) {
                Storage::disk('local')->delete($product->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        $this->clearProductsCache();

        $updatedProduct = $this->productRepository->update($id, $data);

        return $updatedProduct;
    }
    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);
        if (isset($product) && $product->image) {
            Storage::disk('local')->delete($product->image);
        }

        $deletedProduct = $this->productRepository->delete($id);

        $this->clearProductsCache();

        return (bool) $deletedProduct;
    }


    protected function uploadImage(UploadedFile $image): string
    {
        return $image->store('products', 'local');
    }
}
