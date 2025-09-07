<?php declare(strict_types=1);

namespace Domain\Movie;

interface MovieProvider
{
    /**
     * @param string $title
     * @param int $page
     * @return array
     */
    public function searchByTitle(string $title, int $page = 1): array;

    /**
     * @param int $id
     * @return MovieVO|null
     */
    public function findById(int $id): ?MovieVO;
}
