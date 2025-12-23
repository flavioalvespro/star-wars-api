<?php

namespace Tests\Feature;

use App\Models\SearchStatistic;
use App\Services\SearchStatisticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test statistics endpoint returns successful response with data
     */
    public function test_statistics_endpoint_returns_success_with_data(): void
    {
        $mockStatistic = new SearchStatistic([
            'id' => 1,
            'top_queries' => [
                ['term' => 'luke', 'count' => 15, 'percentage' => 50],
                ['term' => 'vader', 'count' => 10, 'percentage' => 33.33],
                ['term' => 'leia', 'count' => 5, 'percentage' => 16.67],
            ],
            'avg_response_time_ms' => 245.67,
            'popular_hours' => [
                ['hour' => 14, 'count' => 20],
                ['hour' => 15, 'count' => 18],
                ['hour' => 16, 'count' => 15],
            ],
            'total_searches' => 30,
            'computed_at' => now(),
        ]);

        $this->mock(SearchStatisticService::class, function ($mock) use ($mockStatistic) {
            $mock->shouldReceive('getLatestStatistics')
                ->once()
                ->andReturn($mockStatistic);
        });

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'top_queries',
                    'average_response_time_ms',
                    'popular_hours',
                    'total_searches',
                    'last_computed_at',
                ],
            ])
            ->assertJsonPath('data.total_searches', 30)
            ->assertJsonPath('data.average_response_time_ms', 245.67)
            ->assertJsonPath('data.top_queries.0.term', 'luke')
            ->assertJsonPath('data.top_queries.0.count', 15)
            ->assertJsonPath('data.top_queries.0.percentage', 50)
            ->assertJsonPath('data.popular_hours.0.hour', 14)
            ->assertJsonPath('data.popular_hours.0.count', 20);
    }

    /**
     * Test statistics endpoint returns message when no data available
     */
    public function test_statistics_endpoint_returns_message_when_no_data(): void
    {
        $this->mock(SearchStatisticService::class, function ($mock) {
            $mock->shouldReceive('getLatestStatistics')
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'No statistics available yet. Statistics are computed every 5 minutes.',
                'data' => null,
            ]);
    }

    /**
     * Test statistics endpoint validates last_computed_at format
     */
    public function test_statistics_endpoint_returns_correct_date_format(): void
    {
        $computedAt = now();
        $mockStatistic = new SearchStatistic([
            'id' => 1,
            'top_queries' => [],
            'avg_response_time_ms' => 100.0,
            'popular_hours' => [],
            'total_searches' => 10,
            'computed_at' => $computedAt,
        ]);

        $this->mock(SearchStatisticService::class, function ($mock) use ($mockStatistic) {
            $mock->shouldReceive('getLatestStatistics')
                ->once()
                ->andReturn($mockStatistic);
        });

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJsonPath('data.last_computed_at', $computedAt->format('Y-m-d H:i:s'));
    }

    /**
     * Test statistics endpoint returns correct structure for top queries
     */
    public function test_statistics_endpoint_returns_correct_top_queries_structure(): void
    {
        $mockStatistic = new SearchStatistic([
            'id' => 1,
            'top_queries' => [
                ['term' => 'skywalker', 'count' => 25, 'percentage' => 62.5],
            ],
            'avg_response_time_ms' => 150.0,
            'popular_hours' => [],
            'total_searches' => 40,
            'computed_at' => now(),
        ]);

        $this->mock(SearchStatisticService::class, function ($mock) use ($mockStatistic) {
            $mock->shouldReceive('getLatestStatistics')
                ->once()
                ->andReturn($mockStatistic);
        });

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'top_queries' => [
                        '*' => [
                            'term',
                            'count',
                            'percentage',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Test statistics endpoint returns correct structure for popular hours
     */
    public function test_statistics_endpoint_returns_correct_popular_hours_structure(): void
    {
        $mockStatistic = new SearchStatistic([
            'id' => 1,
            'top_queries' => [],
            'avg_response_time_ms' => 200.0,
            'popular_hours' => [
                ['hour' => 10, 'count' => 30],
            ],
            'total_searches' => 30,
            'computed_at' => now(),
        ]);

        $this->mock(SearchStatisticService::class, function ($mock) use ($mockStatistic) {
            $mock->shouldReceive('getLatestStatistics')
                ->once()
                ->andReturn($mockStatistic);
        });

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'popular_hours' => [
                        '*' => [
                            'hour',
                            'count',
                        ],
                    ],
                ],
            ]);
    }
}
