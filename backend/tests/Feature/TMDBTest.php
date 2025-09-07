<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Application\Favorite\AddMovieToFavoriteListUseCase;
use Application\Favorite\ListUserFavoriteMoviesUseCase;
use Application\Favorite\RemoveMovieFromFavoriteListUseCase;
use Application\Movie\FindMovieByIdUseCase;
use Application\Movie\SearchMoviesUseCase;
use Domain\Movie\MovieSearchItemDTO;
use Domain\Movie\MovieVO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Infrastructure\TMDB\TMDBClient;
use Tests\TestCase;

final class TMDBTest extends TestCase
{
    use RefreshDatabase;

    private int $myFavoriteMovieId = 603; // The Matrix

    protected function setUp(): void
    {
        parent::setUp();

        // Configuração de cache para testes
        config(['cache.default' => 'array']);
        Cache::flush();

        // Singleton do cliente TMDB
        $this->app->singleton(TMDBClient::class, fn() => new TMDBClient());
    }

    /**
     * Testa a busca de filmes pelo título
     */
    public function test_search_movies(): void
    {
        $useCase = $this->app->make(SearchMoviesUseCase::class);
        $response = $useCase->execute('The Matrix', 1);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        foreach ($response as $movie) {
            $this->assertInstanceOf(MovieSearchItemDTO::class, $movie);
        }
    }

    /**
     * Testa a recuperação dos detalhes de um filme
     */
    public function test_get_movie_details(): void
    {
        $movie = $this->getMovie($this->myFavoriteMovieId);

        $this->assertEquals($this->myFavoriteMovieId, $movie->id);
        $this->assertStringContainsStringIgnoringCase('Matrix', $movie->title);
        $this->assertNotEmpty($movie->posterPath);
        $this->assertNotEmpty($movie->backdropPath);
        $this->assertNotEmpty($movie->releaseDate);
        $this->assertNotEmpty($movie->originCountry);
        $this->assertNotEmpty($movie->genres);
        $this->assertGreaterThan(0, $movie->runtimeMinutes);
        $this->assertNotEmpty($movie->tagline);
        $this->assertNotEmpty($movie->overview);
        $this->assertGreaterThanOrEqual(0, $movie->voteAverage);
        $this->assertGreaterThanOrEqual(0, $movie->voteCount);
    }

    /**
     * Testa adicionar um filme à lista de favoritos
     */
    public function test_add_movie_to_favorite_list(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $movie = $this->getMovie($this->myFavoriteMovieId);
        $this->addFavorite($user->id, $movie);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);
    }

    /**
     * Testa listar favoritos sem filtro de gênero
     */
    public function test_list_user_favorites_without_genre_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $movie = $this->getMovie($this->myFavoriteMovieId);
        $this->addFavorite($user->id, $movie);

        $favorites = $this->listFavorites($user->id);

        $this->assertNotEmpty($favorites);
        $this->assertInstanceOf(MovieVO::class, $favorites[0]->getMovie());
    }

    /**
     * Testa listar favoritos com filtro por gênero
     */
    public function test_list_user_favorites_with_genre_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $movie = $this->getMovie($this->myFavoriteMovieId);
        $this->addFavorite($user->id, $movie);

        $firstGenreId = $movie->genres[0]->id ?? null;
        $this->assertNotNull($firstGenreId, 'Movie should have at least one genre');

        $favorites = $this->listFavorites($user->id, $firstGenreId);

        $this->assertNotEmpty($favorites);
        $this->assertInstanceOf(MovieVO::class, $favorites[0]->getMovie());
    }

    /**
     * Testa remover um filme da lista de favoritos
     */
    public function test_remove_movie_from_favorite_list(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $movie = $this->getMovie($this->myFavoriteMovieId);
        $this->addFavorite($user->id, $movie);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);

        $removeFavorite = $this->app->make(RemoveMovieFromFavoriteListUseCase::class);
        $removeFavorite->execute($user->id, $this->myFavoriteMovieId);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function getMovie(int $movieId): MovieVO
    {
        $useCase = $this->app->make(FindMovieByIdUseCase::class);
        return $useCase->execute($movieId);
    }

    private function addFavorite(int $userId, MovieVO $movie): void
    {
        $useCase = $this->app->make(AddMovieToFavoriteListUseCase::class);
        $useCase->execute($userId, $movie);
    }

    private function listFavorites(int $userId, ?int $genreId = null): array
    {
        $useCase = $this->app->make(ListUserFavoriteMoviesUseCase::class);
        return $useCase->execute($userId, $genreId);
    }
}
