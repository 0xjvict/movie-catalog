<?php declare(strict_types=1);

namespace Domain\Movie;

final class MovieSearchItemDTO
{
    /**
     * @param int $movieId
     * @param string $title
     * @param string $posterPath
     * @param string $releaseDate
     * @param string $overview
     */
    public function __construct(
        public int    $movieId,
        public string $title,
        public string $posterPath,
        public string $releaseDate,
        public string $overview
    )
    {
    }

    /**
     * Retorna a URL completa do poster
     * @param string $baseUrl
     * @return string
     */
    public function posterUrl(string $baseUrl): string
    {
        return $this->posterPath !== ''
            ? rtrim($baseUrl, '/') . '/' . ltrim($this->posterPath, '/')
            : '';
    }
}
