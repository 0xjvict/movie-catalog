<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use Domain\Movie\MovieProvider;
use Domain\Movie\MovieSearchItemDTO;
use Domain\Movie\MovieVO;

final class TMDBMovieProvider implements MovieProvider
{
    /** @var array<int,string>|null */
    private ?array $genreMap = null;

    /**
     * @param TMDBClient $client
     * @param MovieMapper $mapper
     */
    public function __construct(
        private readonly TMDBClient  $client,
        private readonly MovieMapper $mapper
    )
    {
    }

    /**
     * Busca filmes por título.
     *
     * @param string $title
     * @param int $page
     * @return MovieSearchItemDTO[]
     */
    public function searchByTitle(string $title, int $page = 1): array
    {
        $raw = $this->client->searchMovies($title, $page);

        return $this->mapper->mapSearchResponseToDTOList($raw);
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

        return $this->mapper->mapToMovieVO($raw, $this->loadGenreMap());
    }

    /**
     * Carrega mapa de gêneros com cache interno.
     *
     * @return array<int,string>
     */
    private function loadGenreMap(): array
    {
        if ($this->genreMap === null) {
            $this->genreMap = $this->client->getGenresList();
        }

        return $this->genreMap;
    }
}
