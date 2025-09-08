<?php declare(strict_types=1);

namespace Application\Favorite;

final readonly class FavoriteMovieListDTO
{
    public function __construct(
        public int     $id,
        public string  $title,
        public ?string $posterPath,
        public string  $releaseDate,
        public array   $genres,
        public float   $voteAverage
    )
    {
    }
}
