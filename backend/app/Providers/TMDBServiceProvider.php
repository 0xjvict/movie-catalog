<?php

namespace App\Providers;

use Domain\Movie\MovieProvider;
use Illuminate\Support\ServiceProvider;
use Infrastructure\TMDB\MovieMapper;
use Infrastructure\TMDB\TMDBClient;
use Infrastructure\TMDB\TMDBConfig;
use Infrastructure\TMDB\TMDBMovieProvider;
use InvalidArgumentException;

class TMDBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TMDBConfig::class, function () {
            $config = config('tmdb') ?? [];
            return new TMDBConfig(
                apiBaseUrl: $config['api_url'] ?? 'https://api.themoviedb.org/3',
                imageBaseUrl: $config['image_url'] ?? 'https://image.tmdb.org/t/p/w500'
            );
        });

        $this->app->singleton(TMDBClient::class, function () {
            $config = config('tmdb') ?? [];
            $bearerToken = $config['bearer_token'] ?? env('TMDB_BEARER_TOKEN', '');
            $cacheTtl = (int)($config['cache_ttl'] ?? env('TMDB_CACHE_TTL', 3600));

            if (empty($bearerToken)) {
                throw new InvalidArgumentException('TMDB Bearer Token é obrigatório.');
            }

            return new TMDBClient(
                bearerToken: $bearerToken,
                cacheTtlSeconds: $cacheTtl
            );
        });

        $this->app->singleton(MovieMapper::class, function ($app) {
            return new MovieMapper($app->make(TMDBConfig::class));
        });

        $this->app->singleton(MovieProvider::class, function ($app) {
            return new TMDBMovieProvider(
                $app->make(TMDBClient::class),
                $app->make(MovieMapper::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
