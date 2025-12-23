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
     * Uses cache to avoid repeated API calls for the same person.
     * Star Wars characters data is static, so we can cache indefinitely.
     *
     * @param int $id
     * @return array|null
     */
    public function getPerson(int $id): ?array
    {
        $cacheKey = $this->makeCacheKey('id', (string)$id);

        return $this->cacheRemember($cacheKey, function () use ($id) {
            return $this->getById($id);
        });
    }

    /**
     * Search people by name
     *
     * Uses cache to avoid repeated API calls for the same search term.
     * This dramatically improves performance for popular searches like "Luke" or "Vader".
     *
     * @param string $name
     * @return array|null
     */
    public function searchByName(string $name): ?array
    {
        $cacheKey = $this->makeCacheKey('search', $name);

        return $this->cacheRemember($cacheKey, function () use ($name) {
            return $this->get($this->resource, [
                'name' => $name,
            ]);
        });
    }
}
