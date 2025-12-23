<?php

namespace Tests\Feature;

use App\Services\Swapi\FilmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmTest extends TestCase
{
    /**
     * Test search films endpoint returns successful response with valid title
     */
    public function test_search_films_with_valid_title_returns_success(): void
    {
        $mockResponse = [
            'result' => [
                [
                    'uid' => '1',
                    'properties' => [
                        'title' => 'A New Hope',
                        'episode_id' => 4,
                        'opening_crawl' => 'It is a period of civil war...',
                        'director' => 'George Lucas',
                        'producer' => 'Gary Kurtz, Rick McCallum',
                        'release_date' => '1977-05-25',
                        'characters' => ['https://www.swapi.tech/api/people/1'],
                        'planets' => ['https://www.swapi.tech/api/planets/1'],
                        'starships' => ['https://www.swapi.tech/api/starships/2'],
                        'vehicles' => ['https://www.swapi.tech/api/vehicles/4'],
                        'species' => ['https://www.swapi.tech/api/species/1'],
                        'url' => 'https://www.swapi.tech/api/films/1',
                    ],
                ],
            ],
        ];

        $this->mock(FilmService::class, function ($mock) use ($mockResponse) {
            $mock->shouldReceive('searchByTitle')
                ->with('Hope')
                ->once()
                ->andReturn($mockResponse);
        });

        $response = $this->getJson('/api/v1/films/search?title=Hope');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'uid',
                        'title',
                        'episode_id',
                        'opening_crawl',
                        'director',
                        'producer',
                        'release_date',
                        'characters',
                        'planets',
                        'starships',
                        'vehicles',
                        'species',
                        'url',
                    ],
                ],
            ])
            ->assertJsonPath('data.0.title', 'A New Hope');
    }

    /**
     * Test search films endpoint returns validation error without title
     */
    public function test_search_films_without_title_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/films/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test search films endpoint returns validation error with short title
     */
    public function test_search_films_with_short_title_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/films/search?title=A');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test search films endpoint returns message when no results found
     */
    public function test_search_films_returns_message_when_no_results(): void
    {
        $this->mock(FilmService::class, function ($mock) {
            $mock->shouldReceive('searchByTitle')
                ->with('NonExistentFilm')
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/v1/films/search?title=NonExistentFilm');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'No results found for the search term.',
                'data' => null
            ]);
    }

    /**
     * Test get film by id endpoint returns successful response
     */
    public function test_get_film_by_id_returns_success(): void
    {
        $mockResponse = [
            'result' => [
                'uid' => '1',
                'properties' => [
                    'title' => 'A New Hope',
                    'episode_id' => 4,
                    'opening_crawl' => 'It is a period of civil war...',
                    'director' => 'George Lucas',
                    'producer' => 'Gary Kurtz, Rick McCallum',
                    'release_date' => '1977-05-25',
                    'characters' => ['https://www.swapi.tech/api/people/1'],
                    'planets' => ['https://www.swapi.tech/api/planets/1'],
                    'starships' => ['https://www.swapi.tech/api/starships/2'],
                    'vehicles' => ['https://www.swapi.tech/api/vehicles/4'],
                    'species' => ['https://www.swapi.tech/api/species/1'],
                    'url' => 'https://www.swapi.tech/api/films/1',
                ],
            ],
        ];

        $this->mock(FilmService::class, function ($mock) use ($mockResponse) {
            $mock->shouldReceive('getFilm')
                ->with(1)
                ->once()
                ->andReturn($mockResponse);
        });

        $response = $this->getJson('/api/v1/films/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'uid',
                    'title',
                    'episode_id',
                    'opening_crawl',
                    'director',
                    'producer',
                    'release_date',
                    'characters',
                    'planets',
                    'starships',
                    'vehicles',
                    'species',
                    'url',
                ],
            ])
            ->assertJsonPath('data.title', 'A New Hope')
            ->assertJsonPath('data.uid', '1');
    }

    /**
     * Test get film by id endpoint returns 404 when film not found
     */
    public function test_get_film_by_id_returns_404_when_not_found(): void
    {
        $this->mock(FilmService::class, function ($mock) {
            $mock->shouldReceive('getFilm')
                ->with(9999)
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/v1/films/9999');

        $response->assertStatus(404);
    }

    /**
     * Test get film by id endpoint returns 404 when result is empty
     */
    public function test_get_film_by_id_returns_404_when_result_empty(): void
    {
        $this->mock(FilmService::class, function ($mock) {
            $mock->shouldReceive('getFilm')
                ->with(9999)
                ->once()
                ->andReturn([]);
        });

        $response = $this->getJson('/api/v1/films/9999');

        $response->assertStatus(404);
    }
}
