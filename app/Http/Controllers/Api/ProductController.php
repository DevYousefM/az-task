<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getPaginatedProducts();

        return ApiResponse::success($products);
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productService->createNewProduct($request);

        return ApiResponse::success($product);
    }
    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return ApiResponse::error([], 'Product not found', 404);
        }

        return ApiResponse::success($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        if (!$product) {
            return ApiResponse::error([], 'Product not found', 404);
        }
        return ApiResponse::success($product);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return ApiResponse::success([], 'Product deleted successfully');
    }
}
