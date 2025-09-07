<?php declare(strict_types=1);

namespace Tests\Feature;

use Application\Movie\FindMovieByIdUseCase;
use Application\Movie\SearchMoviesUseCase;
use Domain\Movie\MovieVO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Infrastructure\TMDB\TMDBClient;
use Tests\TestCase;

final class TMDBTest extends TestCase
{
    use RefreshDatabase;

    private int $myFavoriteMovieId = 603;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::flush();

        $this->app->singleton(TMDBClient::class, function () {
            return new TMDBClient();
        });
    }

    public function test_search_movies(): void
    {
        $useCase = $this->app->make(SearchMoviesUseCase::class);
        $response = $useCase->execute('The Matrix', 1);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        foreach ($response as $movie) {
            $this->assertInstanceOf(MovieVO::class, $movie);
        }
    }

    public function test_get_movie_details(): void
    {
        $useCase = $this->app->make(FindMovieByIdUseCase::class);
        $movie = $useCase->execute($this->myFavoriteMovieId);

        $this->assertInstanceOf(MovieVO::class, $movie);
        $this->assertEquals($this->myFavoriteMovieId, $movie->id);
        $this->assertEquals('Matrix', $movie->title);
        $this->assertNotEmpty($movie->overview);
        $this->assertNotEmpty($movie->posterPath);
        $this->assertNotEmpty($movie->backdropPath);
        $this->assertNotEmpty($movie->releaseDate);
        $this->assertNotEmpty($movie->genres);
    }
}
