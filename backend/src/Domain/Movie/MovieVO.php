<?php declare(strict_types=1);

namespace Domain\Movie;

final readonly class MovieVO
{
    /**
     * @param int $id
     * @param string $title
     * @param string|null $overview
     * @param string|null $posterPath
     * @param string|null $backdropPath
     * @param string|null $releaseDate
     * @param Genre[] $genres
     */
    public function __construct(
        public int     $id,
        public string  $title,
        public ?string $overview,
        public ?string $posterPath,
        public ?string $backdropPath,
        public ?string $releaseDate,
        public array   $genres
    )
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getOverview(): ?string
    {
        return $this->overview;
    }

    /**
     * @return string|null
     */
    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    /**
     * @return string|null
     */
    public function getBackdropPath(): ?string
    {
        return $this->backdropPath;
    }

    /**
     * @return string|null
     */
    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }

    /**
     * @return Genre[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }
}
