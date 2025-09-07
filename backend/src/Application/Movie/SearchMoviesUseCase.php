<?php declare(strict_types=1);

namespace Application\Movie;

use Domain\Movie\MovieProvider;
use Domain\Movie\MovieVO;

final readonly class SearchMoviesUseCase
{
    public function __construct(
        private MovieProvider $movieProvider
    )
    {
    }

    /**
     * @return MovieVO[]
     */
    public function execute(string $title, int $page = 1): array
    {
        return $this->movieProvider->searchByTitle($title, $page);
    }
}
