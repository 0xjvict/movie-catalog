<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Application\Favorite\AddMovieToFavoriteListUseCase;
use Application\Favorite\ListUserFavoriteMoviesUseCase;
use Application\Favorite\RemoveMovieFromFavoriteListUseCase;
use Application\Movie\FindMovieByIdUseCase;
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

        config(['cache.default' => 'array']);
        Cache::flush();

        $this->app->singleton(TMDBClient::class, fn() => new TMDBClient());
    }

    public function test_search_movies(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->getJson('/api/movies/search?q=The+Matrix&page=1');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        foreach ($data as $movie) {
            $this->assertArrayHasKey('movieId', $movie);
            $this->assertArrayHasKey('title', $movie);
            $this->assertArrayHasKey('posterPath', $movie);
            $this->assertArrayHasKey('releaseDate', $movie);
            $this->assertArrayHasKey('overview', $movie);
        }
    }

    public function test_get_movie_details(): void
    {
        $movie = $this->getMovie($this->myFavoriteMovieId);

        $this->assertInstanceOf(MovieVO::class, $movie);
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

    public function test_add_movie_to_favorite_list(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->addFavorite($user->id, $this->myFavoriteMovieId);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);
    }

    public function test_list_user_favorites_without_genre_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->addFavorite($user->id, $this->myFavoriteMovieId);

        $favorites = $this->listFavorites($user->id);

        $this->assertNotEmpty($favorites);
        $this->assertInstanceOf(MovieVO::class, $favorites[0]->getMovie());
    }

    public function test_list_user_favorites_with_genre_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->addFavorite($user->id, $this->myFavoriteMovieId);

        $movie = $this->getMovie($this->myFavoriteMovieId);
        $genreId = $movie->genres[0]->id ?? null;
        $this->assertNotNull($genreId, 'Movie deve ter pelo menos um gênero');

        $favorites = $this->listFavorites($user->id, $genreId);

        $this->assertNotEmpty($favorites);
        $this->assertInstanceOf(MovieVO::class, $favorites[0]->getMovie());
    }

    public function test_remove_movie_from_favorite_list(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->addFavorite($user->id, $this->myFavoriteMovieId);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);

        $this->removeFavorite($user->id, $this->myFavoriteMovieId);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'tmdb_id' => $this->myFavoriteMovieId,
        ]);
    }

    /**
     * Helpers
     */

    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function getMovie(int $movieId): ?MovieVO
    {
        return $this->app->make(FindMovieByIdUseCase::class)($movieId);
    }

    private function addFavorite(int $userId, int $movieId): void
    {
        $this->app->make(AddMovieToFavoriteListUseCase::class)($userId, $movieId);
    }

    private function listFavorites(int $userId, ?int $genreId = null): array
    {
        return $this->app->make(ListUserFavoriteMoviesUseCase::class)($userId, $genreId);
    }

    private function removeFavorite(int $userId, int $tmdbId): void
    {
        $this->app->make(RemoveMovieFromFavoriteListUseCase::class)($userId, $tmdbId);
    }
}
