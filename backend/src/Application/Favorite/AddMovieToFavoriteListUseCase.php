<?php declare(strict_types=1);

namespace Application\Favorite;

use Domain\Favorite\Favorite;
use Domain\Favorite\FavoriteRepository;
use Domain\Movie\MovieVO;

final readonly class AddMovieToFavoriteListUseCase
{
    /**
     * @param FavoriteRepository $repository
     */
    public function __construct(private FavoriteRepository $repository)
    {
    }

    /**
     * @param int $userId
     * @param MovieVO $movie
     */
    public function execute(int $userId, MovieVO $movie): void
    {
        $this->repository->save(new Favorite(
            userId: $userId,
            movie: $movie
        ));
    }
}
