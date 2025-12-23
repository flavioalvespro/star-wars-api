<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeopleResource extends JsonResource
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
            'name' => $hasProperties
                ? ($this->resource['properties']['name'] ?? null)
                : ($this->resource['name'] ?? null),
            'gender' => $this->resource['properties']['gender'] ?? null,
            'birth_year' => $this->resource['properties']['birth_year'] ?? null,
            'height' => $this->resource['properties']['height'] ?? null,
            'mass' => $this->resource['properties']['mass'] ?? null,
            'hair_color' => $this->resource['properties']['hair_color'] ?? null,
            'skin_color' => $this->resource['properties']['skin_color'] ?? null,
            'eye_color' => $this->resource['properties']['eye_color'] ?? null,
            'homeworld' => $this->resource['properties']['homeworld'] ?? null,
            'url' => $hasProperties
                ? ($this->resource['properties']['url'] ?? null)
                : ($this->resource['url'] ?? null),
        ];
    }
}
