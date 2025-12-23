<?php

namespace App\Jobs;

use App\Models\SearchLog;
use App\Models\SearchStatistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComputeSearchStatistics implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $totalSearches = SearchLog::count();
        
        if ($totalSearches === 0) {
            return;
        }

        // a. Top 5 queries with percentages
        $topQueries = SearchLog::select('search_term', DB::raw('COUNT(*) as count'))
            ->whereNotNull('search_term')
            ->groupBy('search_term')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($query) use ($totalSearches) {
                return [
                    'term' => $query->search_term,
                    'count' => $query->count,
                    'percentage' => round(($query->count / $totalSearches) * 100, 2)
                ];
            });

        // b. average response time in ms
        $avgResponseTime = SearchLog::avg('response_time_ms');

        // c. most popular hour (search volume per hour)
        $popularHours = SearchLog::select(
                DB::raw('HOUR(searched_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'count' => $item->count
                ];
            });

        // save statiscts computed
        SearchStatistic::create([
            'top_queries' => $topQueries,
            'avg_response_time_ms' => round($avgResponseTime, 2),
            'popular_hours' => $popularHours,
            'total_searches' => $totalSearches,
            'computed_at' => now(),
        ]);
    }
}
