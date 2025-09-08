<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

final readonly class TMDBConfig
{
    public function __construct(
        private string $apiBaseUrl,
        private string $imageBaseUrl
    )
    {
    }

    public function getApiBaseUrl(): string
    {
        return rtrim($this->apiBaseUrl, '/');
    }

    public function getImageBaseUrl(): string
    {
        return rtrim($this->imageBaseUrl, '/');
    }
}
