<?php

namespace App\Services\Swapi;

class FilmService extends BaseService
{
    /**
     * The resource endpoint
     */
    protected string $resource = 'films';

    /**
     * Get a film by ID
     *
     * CACHE: Saves to cache to avoid repeated API requests
     * Cache key: "swapi:films:id:1" (example for ID 1)
     *
     * @param int $id Film ID
     * @return array|null Film data or null if not found
     */
    public function getFilm(int $id): ?array
    {
        $cacheKey = $this->makeCacheKey('id', $id);

        // Retrieve from cache or fetch from API
        return $this->getFromCacheOrFetch($cacheKey, function () use ($id) {
            return $this->getById($id);
        });
    }

    /**
     * Search films by title
     *
     * CACHE: Saves to cache to avoid repeated API requests
     * Cache key: "swapi:films:search:hope" (example for "Hope" search)
     *
     * @param string $title Title or partial title to search
     * @return array|null List of films found or null if error
     */
    public function searchByTitle(string $title): ?array
    {
        $cacheKey = $this->makeCacheKey('search', $title);

        // Retrieve from cache or fetch from API
        return $this->getFromCacheOrFetch($cacheKey, function () use ($title) {
            return $this->get($this->resource, [
                'title' => $title,
            ]);
        });
    }
}
