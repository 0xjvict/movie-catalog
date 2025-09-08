<?php declare(strict_types=1);

namespace Application\Movie;

use Domain\Movie\MovieProvider;
use Domain\Movie\MovieVO;

final readonly class FindMovieByIdUseCase
{
    public function __construct(
        private MovieProvider $movieProvider
    )
    {
    }

    public function __invoke(int $movieId): ?MovieVO
    {
        return $this->movieProvider->findById($movieId);
    }
}
