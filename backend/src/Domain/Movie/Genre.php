<?php declare(strict_types=1);

namespace Domain\Movie;

final readonly class Genre
{
    public function __construct(
        public int    $id,
        public string $name
    )
    {
    }
}
