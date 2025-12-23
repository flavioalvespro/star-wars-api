<?php

namespace App\Services\Swapi;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

abstract class BaseService
{
    /**
     * The base URL for SWAPI API
     */
    protected string $baseUrl;

    /**
     * The resource endpoint (e.g., 'people', 'films')
     */
    protected string $resource;

    /**
     * Cache TTL in seconds (default: 1 hour)
     * Star Wars data is static, so we can cache for a long time
     */
    protected int $cacheTTL = 3600;

    /**
     * Cache key prefix for SWAPI data
     */
    protected string $cachePrefix = 'swapi';

    /**
     * Create a new service instance
     */
    public function __construct()
    {
        $this->baseUrl = config('services.swapi.base_url', env('SWAPI_BASE_URL'));
    }

    /**
     * Make a GET request to SWAPI API
     *
     * @param string $endpoint
     * @param array $params
     * @return array|null
     */
    protected function get(string $endpoint, array $params = []): ?array
    {
        try {
            $response = Http::baseUrl($this->baseUrl)
                ->timeout(30)
                ->retry(3, 100)
                ->get($endpoint, $params);

            
            return $response->json();
        } catch (RequestException $e) {
            report($e);

            return null;
        }
    }

    /**
     * Get a single resource by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->get("{$this->resource}/{$id}");
    }

    /**
     * Get all resources with pagination
     *
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    public function getAll(int $page = 1, int $limit = 10): ?array
    {
        return $this->get($this->resource, [
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Search for resources
     *
     * @param string $query
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    public function search(string $query, int $page = 1, int $limit = 10): ?array
    {
        return $this->get($this->resource, [
            'search' => $query,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Cache helper - Remember data in cache or execute callback
     *
     * This method implements the cache-aside pattern:
     * 1. Check if data exists in cache
     * 2. If yes, return cached data (fast!)
     * 3. If no, execute callback to fetch data
     * 4. Store result in cache
     * 5. Return result
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if cache miss
     * @param int|null $ttl Time to live in seconds (null = use default)
     * @return mixed
     */
    protected function cacheRemember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->cacheTTL;

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Generate a cache key for SWAPI data
     *
     * @param string ...$parts Key parts to join
     * @return string
     */
    protected function makeCacheKey(string ...$parts): string
    {
        return implode(':', array_merge(
            [$this->cachePrefix, $this->resource],
            array_map('strtolower', $parts)
        ));
    }
}
