<?php

namespace App\Services\Swapi;

class PeopleService extends BaseService
{
    /**
     * The resource endpoint
     */
    protected string $resource = 'people';

    /**
     * Get a person by ID
     *
     * CACHE: Saves to cache to avoid repeated API requests
     * Cache key: "swapi:people:id:1" (example for ID 1)
     *
     * @param int $id Person ID
     * @return array|null Person data or null if not found
     */
    public function getPerson(int $id): ?array
    {
        $cacheKey = $this->makeCacheKey('id', $id);

        // Retrieve from cache or fetch from API
        return $this->getFromCacheOrFetch($cacheKey, function () use ($id) {
            return $this->getById($id);
        });
    }

    /**
     * Search people by name
     *
     * CACHE: Saves to cache to avoid repeated API requests
     * Cache key: "swapi:people:search:luke" (example for "Luke" search)
     *
     * @param string $name Name or partial name to search
     * @return array|null List of people found or null if error
     */
    public function searchByName(string $name): ?array
    {
        $cacheKey = $this->makeCacheKey('search', $name);

        // Retrieve from cache or fetch from API
        return $this->getFromCacheOrFetch($cacheKey, function () use ($name) {
            return $this->get($this->resource, [
                'name' => $name,
            ]);
        });
    }
}
