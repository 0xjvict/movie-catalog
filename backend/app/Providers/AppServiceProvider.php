<?php

namespace App\Providers;

use Domain\Movie\MovieProvider;
use Illuminate\Support\ServiceProvider;
use Infrastructure\TMDB\TMDBClient;
use Infrastructure\TMDB\TMDBMovieProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TMDBClient::class, function ($app) {
            return new TMDBClient(
                env('TMDB_BEARER_TOKEN'),
                (int)env('TMDB_CACHE_TTL', 3600)
            );
        });

        $this->app->bind(
            MovieProvider::class,
            TMDBMovieProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
