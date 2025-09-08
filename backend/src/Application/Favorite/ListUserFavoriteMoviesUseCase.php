<?php declare(strict_types=1);

namespace Application\Favorite;

use Domain\Favorite\Favorite;
use Domain\Favorite\FavoriteRepository;

final readonly class ListUserFavoriteMoviesUseCase
{
    public function __construct(
        private FavoriteRepository $repository
    )
    {
    }

    /**
     * Lista os filmes favoritos de um usuário, opcionalmente filtrando por gênero.
     *
     * @param int $userId
     * @param int|null $genreId
     * @return Favorite[]|array
     */
    public function __invoke(int $userId, ?int $genreId = null): array
    {
        return $genreId === null
            ? $this->repository->findByUser($userId)
            : $this->repository->findByUserAndGenre($userId, $genreId);
    }
}
