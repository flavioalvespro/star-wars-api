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
     * Uses cache to avoid repeated API calls for the same film.
     * Star Wars films data is static, so we can cache indefinitely.
     *
     * @param int $id
     * @return array|null
     */
    public function getFilm(int $id): ?array
    {
        $cacheKey = $this->makeCacheKey('id', (string) $id);

        return $this->cacheRemember($cacheKey, function () use ($id) {
            return $this->getById($id);
        });
    }

    /**
     * Search films by title
     *
     * Uses cache to avoid repeated API calls for the same search term.
     * This dramatically improves performance for popular searches like "Hope" or "Empire".
     *
     * @param string $title
     * @return array|null
     */
    public function searchByTitle(string $title): ?array
    {
        $cacheKey = $this->makeCacheKey('search', $title);

        return $this->cacheRemember($cacheKey, function () use ($title) {
            return $this->get($this->resource, [
                'title' => $title,
            ]);
        });
    }
}
