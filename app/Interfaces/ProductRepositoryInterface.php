<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Product;


interface ProductRepositoryInterface
{
    /**
     * Get paginated products
     */
    public function getPaginatedProducts(int $perPage = 10, int $page = 1): LengthAwarePaginator;

    /**
     * Find a product by ID
     */
    public function find(int $id): ?object;

    /**
     * Create a new product
     */
    public function create(array $data): object;

    /**
     * Update a product
     */
    public function update(int $id, array $data): ?object;

    /**
     * Delete a product
     */
    public function delete(int $id): bool;
}
