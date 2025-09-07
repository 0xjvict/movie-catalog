<?php declare(strict_types=1);

namespace Domain\Favorite;

use Domain\Movie\MovieVO;

final readonly class Favorite
{
    /**
     * @param int $userId
     * @param MovieVO $movie
     */
    public function __construct(
        private int     $userId,
        private MovieVO $movie
    )
    {
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return MovieVO
     */
    public function getMovie(): MovieVO
    {
        return $this->movie;
    }
}
