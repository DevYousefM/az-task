<?php

namespace App\Providers;

use App\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\CacheService;
use App\Services\ImageService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interface to implementation
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // Register services
        $this->app->singleton(CacheService::class);
        $this->app->singleton(ImageService::class);

        // Register repository with dependencies
        $this->app->singleton(ProductRepository::class, function ($app) {
            return new ProductRepository($app->make(CacheService::class));
        });

        // Register service with dependencies
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(ImageService::class),
                $app->make(CacheService::class)
            );
        });
    }

    public function boot(): void {}
}
