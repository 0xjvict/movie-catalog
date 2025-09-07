<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use Domain\Movie\MovieSearchItemDTO;
use Domain\Movie\MovieVO;

final class MovieMapper
{
    /**
     * @param array $data
     * @return MovieSearchItemDTO
     */
    public static function mapToSearchItemDTO(array $data): MovieSearchItemDTO
    {
        return new MovieSearchItemDTO(
            movieId: (int)($data['id'] ?? 0),
            title: (string)($data['title'] ?? ($data['original_title'] ?? '')),
            posterPath: (string)($data['poster_path'] ?? ''),
            releaseDate: (string)($data['release_date'] ?? ''),
            overview: (string)($data['overview'] ?? '')
        );
    }

    /**
     * @param array $searchResponse
     * @return MovieSearchItemDTO[]
     */
    public static function mapSearchResponseToDTOList(array $searchResponse): array
    {
        $items = $searchResponse['results'] ?? [];
        if (!is_array($items)) {
            return [];
        }

        return array_map(fn($item) => self::mapToSearchItemDTO($item), $items);
    }

    /**
     * @param array $data
     * @param array $genreMap
     * @return MovieVO
     */
    public static function mapToMovieVO(array $data, array $genreMap = []): MovieVO
    {
        $genres = [];

        if (!empty($data['genres'])) {
            foreach ($data['genres'] as $g) {
                $genres[] = $g['name'] ?? ($genreMap[$g['id']] ?? '');
            }
        } elseif (!empty($data['genre_ids'])) {
            foreach ($data['genre_ids'] as $gid) {
                if (isset($genreMap[$gid])) {
                    $genres[] = $genreMap[$gid];
                }
            }
        }

        return new MovieVO(
            id: (int)($data['id'] ?? 0),
            title: (string)($data['title'] ?? ($data['original_title'] ?? '')),
            posterPath: (string)($data['poster_path'] ?? ''),
            backdropPath: (string)($data['backdrop_path'] ?? ''),
            releaseDate: (string)($data['release_date'] ?? ''),
            originCountry: $data['origin_country'][0] ?? ($data['production_countries'][0]['iso_3166_1'] ?? 'N/A'),
            genres: array_filter($genres),
            runtimeMinutes: (int)($data['runtime'] ?? 0),
            tagline: (string)($data['tagline'] ?? ''),
            overview: $data['overview'] ?? null,
            voteAverage: (float)($data['vote_average'] ?? 0),
            voteCount: (int)($data['vote_count'] ?? 0)
        );
    }
}
