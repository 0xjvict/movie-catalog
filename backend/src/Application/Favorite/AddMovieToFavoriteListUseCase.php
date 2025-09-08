<?php declare(strict_types=1);

namespace Application\Favorite;

use Domain\Favorite\Favorite;
use Domain\Favorite\FavoriteRepository;
use Domain\Movie\MovieProvider;
use DomainException;

final readonly class AddMovieToFavoriteListUseCase
{
    /**
     * @param MovieProvider $movieProvider
     * @param FavoriteRepository $repository
     */
    public function __construct(
        private MovieProvider      $movieProvider,
        private FavoriteRepository $repository
    )
    {
    }

    /**
     * @param int $userId
     * @param int $movieId
     */
    public function __invoke(int $userId, int $movieId): void
    {
        $movie = $this->movieProvider->findById($movieId);

        if ($movie === null) {
            throw new DomainException("Filme {$movieId} não encontrado.");
        }

        $alreadyFavorited = $this->repository->exists($userId, $movieId);
        if ($alreadyFavorited) {
            throw new DomainException("Filme já favoritado.");
        }

        $this->repository->save(new Favorite(
            userId: $userId,
            movie: $movie
        ));
    }
}
