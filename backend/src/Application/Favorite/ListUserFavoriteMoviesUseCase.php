<?php declare(strict_types=1);

namespace Application\Favorite;

use Domain\Favorite\Favorite;
use Domain\Favorite\FavoriteRepository;

final readonly class ListUserFavoriteMoviesUseCase
{
    public function __construct(private FavoriteRepository $repository)
    {
    }

    /**
     * @param int $userId
     * @param int|null $genreId
     * @return Favorite[]
     */
    public function execute(int $userId, ?int $genreId = null): array
    {
        if ($genreId === null) {
            return $this->repository->findByUser($userId);
        }

        return $this->repository->findByUserAndGenre($userId, $genreId);
    }
}
