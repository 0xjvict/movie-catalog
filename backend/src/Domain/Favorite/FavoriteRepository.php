<?php declare(strict_types=1);

namespace Domain\Favorite;

interface FavoriteRepository
{
    /**
     * @param Favorite $favorite
     * @return void
     */
    public function save(Favorite $favorite): void;

    /**
     * @param Favorite $favorite
     * @return void
     */
    public function remove(Favorite $favorite): void;

    /**
     * @param int $userId
     * @return Favorite[]|array
     */
    public function findByUser(int $userId): array;

    /**
     * @param int $userId
     * @param int|null $genreId
     * @return Favorite[]|array
     */
    public function findByUserAndGenre(int $userId, ?int $genreId = null): array;

    /**
     * @param int $userId
     * @param int $tmdbId
     * @return Favorite|null
     */
    public function findByUserAndMovie(int $userId, int $tmdbId): ?Favorite;

    public function exists(int $userId, int $movieId): bool;
}
