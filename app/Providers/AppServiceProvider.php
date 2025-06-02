<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductRepository::class, function () {
            return new ProductRepository();
        });
        $this->app->singleton(ProductService::class, function () {
            return new ProductService($this->app->make(ProductRepository::class));
        });
    }


    public function boot(): void {}
}
