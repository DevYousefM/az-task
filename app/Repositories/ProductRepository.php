<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductRepository implements ProductRepositoryInterface
{
    protected $table = 'products';
    protected $cacheTtl = 60 * 60;

    public function getPaginatedProducts(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $offset = ($page - 1) * $perPage;
        $cacheKey =  "products_page_{$page}_per_page_{$perPage}";

        $query = DB::table($this->table)->orderBy('id', 'desc');
        $total = Cache::remember('products_total', $this->cacheTtl, function () use ($query) {
            return $query->count();
        });

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $total, $page, $perPage, $offset) {

            $results = $query->skip($offset)->take($perPage)->get();

            return new Paginator(
                $results,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(),]
            );
        });
    }


    public function find(int $id)
    {
        $cacheKey = "product_{$id}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return DB::table($this->table)->where('id', $id)->first();
        });
    }
    public function create(array $data)
    {
        $id = DB::table($this->table)->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'image' => $data['image'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $this->find($id);
    }
    public function update(int $id, array $data)
    {
        DB::table($this->table)->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        Cache::forget("product_{$id}");

        return $this->find($id);
    }

    public function delete(int $id)
    {
        $deleted = DB::table($this->table)->where('id', $id)->delete();

        return (bool) $deleted;
    }
}
