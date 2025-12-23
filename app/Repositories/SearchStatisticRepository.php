<?php

namespace App\Repositories;

use App\Models\SearchStatistic;

class SearchStatisticRepository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected SearchStatistic $model
    ) {}

    /**
     * Get the latest computed statistics
     *
     * @return SearchStatistic|null
     */
    public function getLatest(): ?SearchStatistic
    {
        return $this->model
            ->latest('computed_at')
            ->first();
    }

    /**
     * Get all statistics ordered by computed date
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->latest('computed_at')
            ->limit($limit)
            ->get();
    }
}
