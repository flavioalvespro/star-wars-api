<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if we have detailed data (from search) or basic data (from list)
        $hasProperties = isset($this->resource['properties']);

        return [
            'uid' => $this->resource['uid'] ?? null,
            'title' => $hasProperties
                ? ($this->resource['properties']['title'] ?? null)
                : ($this->resource['title'] ?? null),
            'episode_id' => $this->resource['properties']['episode_id'] ?? null,
            'opening_crawl' => $this->resource['properties']['opening_crawl'] ?? null,
            'director' => $this->resource['properties']['director'] ?? null,
            'producer' => $this->resource['properties']['producer'] ?? null,
            'release_date' => $this->resource['properties']['release_date'] ?? null,
            'characters' => $this->resource['properties']['characters'] ?? null,
            'planets' => $this->resource['properties']['planets'] ?? null,
            'starships' => $this->resource['properties']['starships'] ?? null,
            'vehicles' => $this->resource['properties']['vehicles'] ?? null,
            'species' => $this->resource['properties']['species'] ?? null,
            'url' => $hasProperties
                ? ($this->resource['properties']['url'] ?? null)
                : ($this->resource['url'] ?? null),
        ];
    }
}
