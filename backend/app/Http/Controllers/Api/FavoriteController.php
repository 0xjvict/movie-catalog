<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Application\Favorite\AddMovieToFavoriteListUseCase;
use Application\Favorite\ListUserFavoriteMoviesUseCase;
use Application\Favorite\RemoveMovieFromFavoriteListUseCase;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Persistence\FavoriteMapper;
use Throwable;

final class FavoriteController extends Controller
{
    public function __construct(
        private readonly AddMovieToFavoriteListUseCase      $addMovieToFavoriteListUseCase,
        private readonly ListUserFavoriteMoviesUseCase      $listUserFavoriteMoviesUseCase,
        private readonly RemoveMovieFromFavoriteListUseCase $removeMovieFromFavoriteListUseCase,
        private readonly FavoriteMapper                     $favoriteMapper
    )
    {

    }

    /**
     * Favorita um filme para o usuário autenticado.
     * Route: POST /api/favorites
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function favorite(Request $request): JsonResponse
    {
        try {
            ($this->addMovieToFavoriteListUseCase)($request->user()->id, $request->input('movie_id'));
            return response()->json([
                'success' => true,
                'message' => 'Filme favoritado com sucesso.'
            ], 201);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        } catch (Throwable) {
            return response()->json([
                'success' => false,
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Lita os filmes favoritados pelo usuário autenticado.
     * Route: GET /api/favorites
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $favoriteMovies = ($this->listUserFavoriteMoviesUseCase)($request->user()->id);
            return response()->json([
                'success' => true,
                'data' => array_map(fn($favorite) => $this->favoriteMapper->toListDTO($favorite), $favoriteMovies),
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        } catch (Throwable) {
            return response()->json([
                'success' => false,
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove um filme dos favoritos do usuário autenticado.
     * Route: DELETE /api/favorites/{tmdbId}
     */
    public function remove(Request $request, int $tmdbId): JsonResponse
    {
        try {
            ($this->removeMovieFromFavoriteListUseCase)($request->user()->id, $tmdbId);
            return response()->json([
                'success' => true,
                'message' => 'Filme removido dos favoritos com sucesso.'
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        } catch (Throwable) {
            return response()->json([
                'success' => false,
                'error' => 'Internal server error'
            ], 500);
        }
    }
}
