<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use Domain\Movie\Genre;
use Domain\Movie\MovieSearchItemDTO;
use Domain\Movie\MovieVO;

/**
 * Responsável por mapear dados da API TMDB para DTOs e VOs
 */
final readonly class MovieMapper
{
    public function __construct(private TMDBConfig $config)
    {
    }

    /**
     * Converte um array de resposta de busca da API em MovieSearchItemDTO
     *
     * @param array $data
     * @return MovieSearchItemDTO
     */
    public function mapToSearchItemDTO(array $data): MovieSearchItemDTO
    {
        return new MovieSearchItemDTO(
            movieId: (int)($data['id'] ?? 0),
            title: (string)($data['title'] ?? ($data['original_title'] ?? '')),
            posterPath: $this->buildImageUrl((string)($data['poster_path'] ?? '')),
            releaseDate: (string)($data['release_date'] ?? ''),
            overview: (string)($data['overview'] ?? '')
        );
    }

    /**
     * Converte a resposta da API de busca em uma lista de MovieSearchItemDTO
     *
     * @param array $searchResponse
     * @return MovieSearchItemDTO[]
     */
    public function mapSearchResponseToDTOList(array $searchResponse): array
    {
        $items = $searchResponse['results'] ?? [];
        if (!is_array($items)) {
            return [];
        }

        return array_map(fn($item) => $this->mapToSearchItemDTO($item), $items);
    }

    /**
     * Converte dados detalhados de filme da API ou do banco em MovieVO
     *
     * @param array $data
     * @param array<int,string> $genreMap
     * @return MovieVO
     */
    public function mapToMovieVO(array $data, array $genreMap = []): MovieVO
    {
        $genres = $this->mapGenres($data, $genreMap);

        return new MovieVO(
            id: (int)($data['id'] ?? 0),
            title: (string)($data['title'] ?? ($data['original_title'] ?? '')),
            posterPath: $this->buildImageUrl((string)($data['poster_path'] ?? '')),
            backdropPath: $this->buildImageUrl((string)($data['backdrop_path'] ?? '')),
            releaseDate: (string)($data['release_date'] ?? ''),
            originCountry: $data['origin_country'][0] ?? ($data['production_countries'][0]['iso_3166_1'] ?? 'N/A'),
            genres: $genres,
            runtimeMinutes: (int)($data['runtime'] ?? 0),
            tagline: (string)($data['tagline'] ?? ''),
            overview: $data['overview'] ?? null,
            voteAverage: (float)($data['vote_average'] ?? 0),
            voteCount: (int)($data['vote_count'] ?? 0)
        );
    }

    /**
     * Retorna a URL completa da imagem do poster
     *
     * @param string $posterPath
     * @return string
     */
    public function getImageUrl(string $posterPath): string
    {
        return $this->buildImageUrl($posterPath);
    }

    /**
     * Constrói URL completa de imagem usando a config
     *
     * @param string $path
     * @return string
     */
    private function buildImageUrl(string $path): string
    {
        if ($path === '') {
            return '';
        }
        return $this->config->getImageBaseUrl() . '/' . ltrim($path, '/');
    }

    /**
     * Mapeia os gêneros do filme
     *
     * @param array $data
     * @param array $genreMap
     * @return Genre[]
     */
    private function mapGenres(array $data, array $genreMap = []): array
    {
        $genres = [];

        // Gêneros detalhados da API
        if (!empty($data['genres'])) {
            foreach ($data['genres'] as $g) {
                if ($g instanceof Genre) {
                    $genres[] = $g;
                } else {
                    $genres[] = new Genre(
                        id: (int)($g['id'] ?? 0),
                        name: (string)($g['name'] ?? ($genreMap[$g['id']] ?? ''))
                    );
                }
            }
        } // Apenas IDs de gênero
        elseif (!empty($data['genre_ids'])) {
            foreach ($data['genre_ids'] as $gid) {
                if (isset($genreMap[$gid])) {
                    $genres[] = new Genre(
                        id: (int)$gid,
                        name: $genreMap[$gid]
                    );
                }
            }
        }

        return $genres;
    }
}
