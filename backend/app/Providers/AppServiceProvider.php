<?php

namespace App\Providers;

use Domain\Favorite\FavoriteRepository;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\EloquentFavoriteRepository;
use Infrastructure\Persistence\FavoriteMapper;
use Infrastructure\TMDB\MovieMapper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FavoriteMapper::class, fn($app) => new FavoriteMapper(
            $app->make(MovieMapper::class)
        ));

        $this->app->bind(FavoriteRepository::class, fn($app) => new EloquentFavoriteRepository(
            $app->make(FavoriteMapper::class)
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
