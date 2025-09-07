<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use Domain\Movie\MovieProvider;
use Domain\Movie\MovieVO;

final class TMDBMovieProvider implements MovieProvider
{
    /**
     * @var TMDBClient
     */
    private TMDBClient $client;
    /** @var array<int,string>|null */
    private ?array $genreMap = null;

    /**
     * @param TMDBClient $client
     */
    public function __construct(TMDBClient $client)
    {
        $this->client = $client;
    }

    /**
     * Carrega o mapa de gêneros do TMDB.
     * @return array<int,string>
     */
    private function loadGenreMap(): array
    {
        if ($this->genreMap === null) {
            $this->genreMap = $this->client->getGenresList();
        }
        return $this->genreMap;
    }

    /**
     * Busca filmes por título.
     *
     * @param string $title
     * @param int $page
     * @return array|MovieVO[]
     */
    public function searchByTitle(string $title, int $page = 1): array
    {
        $raw = $this->client->searchMovies($title, $page);
        $genreMap = $this->loadGenreMap();
        return MovieMapper::fromSearchResponse($raw, $genreMap);
    }

    /**
     * Busca filme por ID.
     *
     * @param int $id
     * @return MovieVO|null
     */
    public function findById(int $id): ?MovieVO
    {
        $raw = $this->client->getMovieDetails($id);
        if (empty($raw)) {
            return null;
        }

        return MovieMapper::fromApi($raw, $this->loadGenreMap());
    }
}
