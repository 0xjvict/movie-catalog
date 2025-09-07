<?php declare(strict_types=1);

namespace Application\Favorite;

use Domain\Favorite\FavoriteRepository;

final readonly class RemoveMovieFromFavoriteListUseCase
{
    public function __construct(private FavoriteRepository $repository)
    {
    }

    /**
     * Remove um filme da lista de favoritos de um usuário
     *
     * @param int $userId
     * @param int $tmdbId
     */
    public function execute(int $userId, int $tmdbId): void
    {
        $favorite = $this->repository->findByUserAndMovie($userId, $tmdbId);
        if ($favorite) {
            $this->repository->remove($favorite);
        }
    }
}
