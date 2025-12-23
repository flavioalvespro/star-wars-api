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
     * Cache TTL in seconds (Time To Live)
     * 3600 seconds = 1 hour
     *
     * Since Star Wars data is static (doesn't change),
     * we can cache it for a long time
     */
    protected int $cacheTTL = 3600;

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
     * Create a unique cache key
     *
     * Key pattern: swapi:{resource}:{type}:{value}
     *
     * Examples:
     * - makeCacheKey('id', 1) → "swapi:people:id:1"
     * - makeCacheKey('search', 'Luke') → "swapi:people:search:luke"
     *
     * @param string $type Search type (id, search, etc)
     * @param string|int $value Search value
     * @return string Formatted cache key
     */
    protected function makeCacheKey(string $type, string|int $value): string
    {
        // Convert to lowercase to ensure consistency
        // "Luke" and "luke" will use the same cache key
        $normalizedValue = strtolower((string) $value);

        return "swapi:{$this->resource}:{$type}:{$normalizedValue}";
    }

    /**
     * Retrieve data from cache or fetch using callback
     *
     * How it works:
     * 1. Check if data exists in cache with the provided key
     * 2. If YES: return cached data (fast!)
     * 3. If NO: execute callback, store in cache, and return data
     *
     * @param string $key Unique key to identify in cache
     * @param callable $callback Function to fetch data when not in cache
     * @return mixed The data (from cache or callback)
     */
    protected function getFromCacheOrFetch(string $key, callable $callback): mixed
    {
        return Cache::remember($key, $this->cacheTTL, $callback);
    }
}
