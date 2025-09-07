<?php declare(strict_types=1);

namespace Application\Movie;

use Domain\Movie\MovieProvider;
use Domain\Movie\MovieSearchItemDTO;

final readonly class SearchMoviesUseCase
{
    public function __construct(
        private MovieProvider $movieProvider
    )
    {
    }

    /**
     * @return MovieSearchItemDTO[]|array
     */
    public function execute(string $title, int $page = 1): array
    {
        return $this->movieProvider->searchByTitle($title, $page);
    }
}
