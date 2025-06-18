<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponse;
use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Get paginated products
     */
    public function index()
    {
        $products = $this->productService->getPaginatedProducts();
        return ApiResponse::success($products);
    }

    /**
     * Create a new product
     */
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createNewProduct($request);
        return ApiResponse::success($product, 'Product created successfully', 201);
    }

    /**
     * Show a specific product
     */
    public function show(int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            return ApiResponse::success($product);
        } catch (ProductNotFoundException $e) {
            return ApiResponse::error([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a product
     */
    public function update(ProductRequest $request, int $id)
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());
            return ApiResponse::success($product, 'Product updated successfully');
        } catch (ProductNotFoundException $e) {
            return ApiResponse::error([], $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a product
     */
    public function destroy(int $id)
    {
        try {
            $this->productService->deleteProduct($id);
            return ApiResponse::success([], 'Product deleted successfully');
        } catch (ProductNotFoundException $e) {
            return ApiResponse::error([], $e->getMessage(), $e->getCode());
        }
    }
}
