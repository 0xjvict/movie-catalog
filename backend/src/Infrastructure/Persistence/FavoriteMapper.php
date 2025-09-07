<?php declare(strict_types=1);

namespace Infrastructure\Persistence;

use Domain\Favorite\Favorite as FavoriteAggregate;
use Domain\Movie\Genre;
use Infrastructure\TMDB\MovieMapper;

/**
 * Responsável por mapear o agregado Favorite para persistência e vice-versa
 */
final class FavoriteMapper
{
    /**
     * Converte o agregado Favorite em array para persistência no banco
     *
     * @param FavoriteAggregate $favorite
     * @return array
     */
    public static function toPersistenceArray(FavoriteAggregate $favorite): array
    {
        $movie = $favorite->getMovie();

        return [
            'tmdb_id' => $movie->id,
            'user_id' => $favorite->getUserId(),
            'title' => $movie->title,
            'poster_path' => $movie->posterPath,
            'backdrop_path' => $movie->backdropPath,
            'release_date' => $movie->releaseDate,
            'origin_country' => $movie->originCountry,
            'runtime_minutes' => $movie->runtimeMinutes,
            'tagline' => $movie->tagline,
            'overview' => $movie->overview,
            'vote_average' => $movie->voteAverage,
            'vote_count' => $movie->voteCount,
        ];
    }

    /**
     * Converte um FavoriteModel do Eloquent em agregado Favorite
     *
     * @param FavoriteModel $model
     * @return FavoriteAggregate
     */
    public static function fromModel(FavoriteModel $model): FavoriteAggregate
    {
        // Mapeia os gêneros do modelo Eloquent para objetos de domínio
        $genres = $model->genres
            ->map(fn($genreModel) => new Genre(
                id: $genreModel->id,
                name: $genreModel->name
            ))
            ->toArray();

        // Prepara os dados do filme para o MovieMapper
        $movieData = [
            'id' => $model->tmdb_id,
            'title' => $model->title,
            'poster_path' => $model->poster_path,
            'backdrop_path' => $model->backdrop_path,
            'release_date' => $model->release_date,
            'origin_country' => $model->origin_country,
            'genres' => $genres,
            'runtime' => $model->runtime_minutes,
            'tagline' => $model->tagline,
            'overview' => $model->overview,
            'vote_average' => $model->vote_average,
            'vote_count' => $model->vote_count,
        ];

        // Cria o agregado Favorite com o MovieVO mapeado
        return new FavoriteAggregate(
            userId: $model->user_id,
            movie: MovieMapper::mapToMovieVO($movieData)
        );
    }
}
