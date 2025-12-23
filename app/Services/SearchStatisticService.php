<?php

namespace App\Services;

use App\Repositories\SearchStatisticRepository;
use App\Models\SearchStatistic;

class SearchStatisticService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected SearchStatisticRepository $repository
    ) {}

    /**
     * Get the latest statistics
     *
     * @return SearchStatistic|null
     */
    public function getLatestStatistics(): ?SearchStatistic
    {
        return $this->repository->getLatest();
    }

    /**
     * Get formatted statistics data
     *
     * @return array
     */
    public function getFormattedStatistics(): array
    {
        $statistics = $this->getLatestStatistics();

        if (!$statistics) {
            return [
                'message' => 'No statistics available yet. Statistics are computed every 5 minutes.',
                'data' => null
            ];
        }

        return [
            'data' => [
                'top_queries' => $statistics->top_queries,
                'average_response_time_ms' => $statistics->avg_response_time_ms,
                'popular_hours' => $statistics->popular_hours,
                'total_searches' => $statistics->total_searches,
                'last_computed_at' => $statistics->computed_at->toIso8601String(),
            ]
        ];
    }

    /**
     * Get statistics history
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStatisticsHistory(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getAll($limit);
    }
}
