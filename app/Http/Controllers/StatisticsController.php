<?php

namespace App\Http\Controllers;

use App\Services\SearchStatisticService;
use App\Http\Resources\SearchStatisticResource;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SearchStatisticService $searchStatisticService
    ) {}

    /**
     * Get the latest search statistics
     *
     * @return JsonResponse|SearchStatisticResource
     */
    public function index(): JsonResponse|SearchStatisticResource
    {
        $statistics = $this->searchStatisticService->getLatestStatistics();

        if (!$statistics) {
            return response()->json([
                'message' => 'No statistics available yet. Statistics are computed every 5 minutes.',
                'data' => null
            ]);
        }

        return new SearchStatisticResource($statistics);
    }
}
