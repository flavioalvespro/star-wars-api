<?php

namespace App\Http\Middleware;

use App\Models\SearchLog;
use Closure;
use Illuminate\Http\Request;

class LogSearchQuery
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        if ($this->shouldLog($request)) {
            $responseTime = (microtime(true) - $startTime) * 1000; // in ms

            $data = $response->getData(true);

            SearchLog::create([
                'entity_type' => $this->getEntityType($request),
                'search_term' => $this->getSearchTerm($request),
                'results_count' => isset($data['data']) ? count($data['data']) : 0,
                'response_time_ms' => round($responseTime, 2),
                'searched_at' => now(),
            ]);
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        // Only log search endpoints, not individual resource requests
        return $request->is('api/v1/people/search') || $request->is('api/v1/films/search');
    }

    private function getEntityType(Request $request): string
    {
        if ($request->is('api/v1/people/*')) return 'people';
        if ($request->is('api/v1/films/*')) return 'films';
        return 'unknown';
    }

    private function getSearchTerm(Request $request): ?string
    {
        // Get 'name' for people or 'title' for films
        return $request->query('name') ?? $request->query('title');
    }
}