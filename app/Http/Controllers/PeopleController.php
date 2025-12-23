<?php

namespace App\Http\Controllers;

use App\Services\Swapi\PeopleService;
use App\Http\Resources\{
    PeopleResource,
    PeopleCollection
};
use App\Http\Requests\SearchPeopleRequest;
use Illuminate\Http\JsonResponse;

class PeopleController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct(
        private PeopleService $peopleService
    ) {}

    /**
     * Search people by name
     *
     * @param SearchPeopleRequest $request
     * @return JsonResponse|PeopleCollection
     */
    public function search(SearchPeopleRequest $request): JsonResponse|PeopleCollection
    {
        $name = $request->input('name');

        $result = $this->peopleService->searchByName($name);

        // Handle empty or null results
        if ($result === null || !isset($result['result'])) {
            return response()->json([
                'message' => 'No results found for the search term.',
                'data' => null
            ]);
        }

        return new PeopleCollection($result['result']);
    }

    /**
     * Get person details by ID
     *
     * @param int $id
     * @return \App\Http\Resources\PeopleResource
     */
    public function show(int $id): PeopleResource
    {
        $result = $this->peopleService->getPerson($id);

        // Handle empty or null results
        if ($result === null || !isset($result['result'])) {
            abort(404, 'Person not found');
        }

        return new PeopleResource($result['result']);
    }
}
