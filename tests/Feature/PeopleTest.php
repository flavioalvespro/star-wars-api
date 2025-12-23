<?php

namespace Tests\Feature;

use App\Services\Swapi\PeopleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeopleTest extends TestCase
{
    /**
     * Test search people endpoint returns successful response with valid name
     */
    public function test_search_people_with_valid_name_returns_success(): void
    {
        $mockResponse = [
            'result' => [
                [
                    'uid' => '1',
                    'properties' => [
                        'name' => 'Luke Skywalker',
                        'gender' => 'male',
                        'birth_year' => '19BBY',
                        'height' => '172',
                        'mass' => '77',
                        'hair_color' => 'blond',
                        'skin_color' => 'fair',
                        'eye_color' => 'blue',
                        'homeworld' => 'https://www.swapi.tech/api/planets/1',
                        'url' => 'https://www.swapi.tech/api/people/1',
                    ],
                ],
            ],
        ];

        $this->mock(PeopleService::class, function ($mock) use ($mockResponse) {
            $mock->shouldReceive('searchByName')
                ->with('Luke')
                ->once()
                ->andReturn($mockResponse);
        });

        $response = $this->getJson('/api/v1/people/search?name=Luke');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'uid',
                        'name',
                        'gender',
                        'birth_year',
                        'height',
                        'mass',
                        'hair_color',
                        'skin_color',
                        'eye_color',
                        'homeworld',
                        'url',
                    ],
                ],
            ])
            ->assertJsonPath('data.0.name', 'Luke Skywalker');
    }

    /**
     * Test search people endpoint returns validation error without name
     */
    public function test_search_people_without_name_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/people/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test search people endpoint returns validation error with short name
     */
    public function test_search_people_with_short_name_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/people/search?name=L');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test search people endpoint returns message when no results found
     */
    public function test_search_people_returns_message_when_no_results(): void
    {
        $this->mock(PeopleService::class, function ($mock) {
            $mock->shouldReceive('searchByName')
                ->with('NonExistentPerson')
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/v1/people/search?name=NonExistentPerson');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'No results found for the search term.',
                'data' => null
            ]);
    }

    /**
     * Test get person by id endpoint returns successful response
     */
    public function test_get_person_by_id_returns_success(): void
    {
        $mockResponse = [
            'result' => [
                'uid' => '1',
                'properties' => [
                    'name' => 'Luke Skywalker',
                    'gender' => 'male',
                    'birth_year' => '19BBY',
                    'height' => '172',
                    'mass' => '77',
                    'hair_color' => 'blond',
                    'skin_color' => 'fair',
                    'eye_color' => 'blue',
                    'homeworld' => 'https://www.swapi.tech/api/planets/1',
                    'url' => 'https://www.swapi.tech/api/people/1',
                ],
            ],
        ];

        $this->mock(PeopleService::class, function ($mock) use ($mockResponse) {
            $mock->shouldReceive('getPerson')
                ->with(1)
                ->once()
                ->andReturn($mockResponse);
        });

        $response = $this->getJson('/api/v1/people/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'uid',
                    'name',
                    'gender',
                    'birth_year',
                    'height',
                    'mass',
                    'hair_color',
                    'skin_color',
                    'eye_color',
                    'homeworld',
                    'url',
                ],
            ])
            ->assertJsonPath('data.name', 'Luke Skywalker')
            ->assertJsonPath('data.uid', '1');
    }

    /**
     * Test get person by id endpoint returns 404 when person not found
     */
    public function test_get_person_by_id_returns_404_when_not_found(): void
    {
        $this->mock(PeopleService::class, function ($mock) {
            $mock->shouldReceive('getPerson')
                ->with(9999)
                ->once()
                ->andReturn(null);
        });

        $response = $this->getJson('/api/v1/people/9999');

        $response->assertStatus(404);
    }

    /**
     * Test get person by id endpoint returns 404 when result is empty
     */
    public function test_get_person_by_id_returns_404_when_result_empty(): void
    {
        $this->mock(PeopleService::class, function ($mock) {
            $mock->shouldReceive('getPerson')
                ->with(9999)
                ->once()
                ->andReturn([]);
        });

        $response = $this->getJson('/api/v1/people/9999');

        $response->assertStatus(404);
    }
}
