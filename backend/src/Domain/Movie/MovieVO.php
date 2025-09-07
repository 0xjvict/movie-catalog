<?php declare(strict_types=1);

namespace Domain\Movie;

use DateTimeImmutable;
use Exception;

/**
 * Value Object que representa os detalhes de um filme
 */
final readonly class MovieVO
{
    /**
     * @param int $id
     * @param string $title
     * @param string $posterPath
     * @param string $backdropPath
     * @param string $releaseDate
     * @param string $originCountry
     * @param Genre[] $genres
     * @param int $runtimeMinutes
     * @param string $tagline
     * @param string|null $overview
     * @param float $voteAverage
     * @param int $voteCount
     */
    public function __construct(
        public int     $id,
        public string  $title,
        public string  $posterPath,
        public string  $backdropPath,
        public string  $releaseDate,
        public string  $originCountry,
        /** @var Genre[] */
        public array   $genres,
        public int     $runtimeMinutes,
        public string  $tagline,
        public ?string $overview,
        public float   $voteAverage,
        public int     $voteCount
    )
    {
    }

    /**
     * Retorna o ano de lançamento
     *
     * @return string
     * @throws Exception
     */
    public function yearRelease(): string
    {
        return (new DateTimeImmutable($this->releaseDate))->format('Y');
    }

    /**
     * Retorna a duração formatada (ex: "2h 16m")
     *
     * @return string
     */
    public function runtimeFormatted(): string
    {
        $h = intdiv($this->runtimeMinutes, 60);
        $m = $this->runtimeMinutes % 60;

        return $h > 0
            ? sprintf('%dh %02dm', $h, $m)
            : sprintf('%dm', $m);
    }

    /**
     * Monta a URL completa do poster
     *
     * @param string $baseUrl
     * @return string
     */
    public function posterUrl(string $baseUrl): string
    {
        return rtrim($baseUrl, '/') . '/' . ltrim($this->posterPath, '/');
    }

    /**
     * Monta a URL completa do backdrop
     *
     * @param string $baseUrl
     * @return string
     */
    public function backdropUrl(string $baseUrl): string
    {
        return rtrim($baseUrl, '/') . '/' . ltrim($this->backdropPath, '/');
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
     * @return string
     */
    public function getPosterPath(): string
    {
        return $this->posterPath;
    }

    /**
     * @return string
     */
    public function getBackdropPath(): string
    {
        return $this->backdropPath;
    }

    /**
     * @return string
     */
    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    /**
     * @return string
     */
    public function getOriginCountry(): string
    {
        return $this->originCountry;
    }

    /**
     * @return Genre[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @return int
     */
    public function getRuntimeMinutes(): int
    {
        return $this->runtimeMinutes;
    }

    /**
     * @return string
     */
    public function getTagline(): string
    {
        return $this->tagline;
    }

    /**
     * @return string|null
     */
    public function getOverview(): ?string
    {
        return $this->overview;
    }

    /**
     * @return float
     */
    public function getVoteAverage(): float
    {
        return $this->voteAverage;
    }

    /**
     * @return int
     */
    public function getVoteCount(): int
    {
        return $this->voteCount;
    }
}
