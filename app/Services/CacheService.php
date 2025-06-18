<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEYS_LIST = 'products:cache_keys';

    /**
     * Remember a cache key for later invalidation
     */
    public function rememberCacheKey(string $key): void
    {
        $keys = Cache::get(self::CACHE_KEYS_LIST, []);
        $keys[] = $key;
        $keys = array_unique($keys);
        Cache::put(self::CACHE_KEYS_LIST, $keys, self::CACHE_TTL);
    }

    /**
     * Clear all product-related cache
     */
    public function clearProductCache(): void
    {
        $cacheKeys = Cache::get(self::CACHE_KEYS_LIST, []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget(self::CACHE_KEYS_LIST);
    }

    /**
     * Get cache with TTL
     */
    public function remember(string $key, \Closure $callback)
    {
        return Cache::remember($key, self::CACHE_TTL, $callback);
    }

    /**
     * Forget specific cache key
     */
    public function forget(string $key): void
    {
        Cache::forget($key);
    }
} 