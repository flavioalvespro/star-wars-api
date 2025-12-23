<?php

namespace App\Http\Controllers;

use App\Services\Swapi\FilmService;
use App\Http\Resources\{
    FilmResource,
    FilmCollection
};
use App\Http\Requests\SearchFilmRequest;
use Illuminate\Http\JsonResponse;

class FilmController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct(
        private FilmService $filmService
    ) {}

    /**
     * Search films by title
     *
     * @param SearchFilmRequest $request
     * @return JsonResponse|FilmCollection
     */
    public function search(SearchFilmRequest $request): JsonResponse|FilmCollection
    {
        $title = $request->input('title');

        $result = $this->filmService->searchByTitle($title);

        // Handle empty or null results
        if ($result === null || !isset($result['result'])) {
            return response()->json([
                'message' => 'No results found for the search term.',
                'data' => null
            ]);
        }

        return new FilmCollection($result['result']);
    }

    /**
     * Get film details by ID
     *
     * @param int $id
     * @return FilmResource
     */
    public function show(int $id): FilmResource
    {
        $result = $this->filmService->getFilm($id);

        // Handle empty or null results
        if ($result === null || !isset($result['result'])) {
            abort(404, 'Film not found');
        }

        return new FilmResource($result['result']);
    }
}
