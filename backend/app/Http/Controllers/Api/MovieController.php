<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Application\Movie\FindMovieByIdUseCase;
use Application\Movie\SearchMoviesUseCase;
use Illuminate\Http\Request;

final class MovieController extends Controller
{
    public function __construct(
        private readonly SearchMoviesUseCase  $searchMoviesUseCase,
        private readonly FindMovieByIdUseCase $findMovieByIdUseCase
    )
    {
    }

    // GET /api/movies/search?q=matrix
    public function search(Request $request)
    {
        $query = $request->query('q', '');

        $movies = ($this->searchMoviesUseCase)($query);

        return response()->json([
            'success' => true,
            'data' => $movies,
            'error' => null
        ]);
    }

    // GET /api/movies/{id}
    public function show(string $id)
    {
        $movie = ($this->findMovieByIdUseCase)($id);

        return response()->json([
            'success' => true,
            'data' => $movie,
            'error' => null
        ]);
    }
}
