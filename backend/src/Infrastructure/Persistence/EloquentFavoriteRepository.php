<?php declare(strict_types=1);

namespace Infrastructure\Persistence;

use Domain\Favorite\Favorite as FavoriteAggregate;
use Domain\Favorite\FavoriteRepository;

final class EloquentFavoriteRepository implements FavoriteRepository
{
    public function save(FavoriteAggregate $favorite): void
    {
        $favoriteModel = FavoriteModel::query()->updateOrCreate(
            ['tmdb_id' => $favorite->getMovie()->id, 'user_id' => $favorite->getUserId()],
            FavoriteMapper::toPersistenceArray($favorite)
        );

        $genreIds = [];
        foreach ($favorite->getMovie()->getGenres() as $genre) {
            $genreModel = GenreModel::query()->updateOrCreate(
                ['id' => $genre->id],
                ['name' => $genre->name]
            );
            $genreIds[] = $genreModel->id;
        }

        $favoriteModel->genres()->sync($genreIds);
    }

    public function remove(FavoriteAggregate $favorite): void
    {
        $favoriteModel = FavoriteModel::query()->where('tmdb_id', $favorite->getMovie()->id)
            ->where('user_id', $favorite->getUserId())
            ->first();

        if ($favoriteModel) {
            $favoriteModel->delete();
        }
    }

    public function findByUser(int $userId): array
    {
        return FavoriteModel::query()
            ->where('user_id', $userId)
            ->with('genres')
            ->get()
            ->map(fn($model) => FavoriteMapper::fromModel($model))
            ->toArray();
    }

    public function findByUserAndGenre(int $userId, ?int $genreId = null): array
    {
        $query = FavoriteModel::query()
            ->where('user_id', $userId)
            ->with('genres');

        if ($genreId !== null) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $genreId));
        }

        return $query->get()
            ->map(fn($model) => FavoriteMapper::fromModel($model))
            ->toArray();
    }

    public function findByUserAndMovie(int $userId, int $tmdbId): ?FavoriteAggregate
    {
        $favoriteModel = FavoriteModel::query()
            ->where('user_id', $userId)
            ->where('tmdb_id', $tmdbId)
            ->with('genres')
            ->first();

        return $favoriteModel ? FavoriteMapper::fromModel($favoriteModel) : null;
    }
}
