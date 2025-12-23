<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'top_queries' => $this->top_queries,
            'average_response_time_ms' => $this->avg_response_time_ms,
            'popular_hours' => $this->popular_hours,
            'total_searches' => $this->total_searches,
            'last_computed_at' => $this->computed_at->format('Y-m-d H:i:s'),
        ];
    }
}
