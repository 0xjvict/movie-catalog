<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use Domain\Movie\MovieVO;

final class MovieMapper
{
    private const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';

    /**
     * Mapeia array de resposta da API para MovieVO
     *
     * @param array $data
     * @param array<int,string> $genreMap optional id=>name mapping
     * @return MovieVO
     */
    public static function fromApi(array $data, array $genreMap = []): MovieVO
    {
        $genres = [];

        if (!empty($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $g) {
                if (isset($g['id'])) {
                    $genres[] = ['id' => (int)$g['id'], 'name' => $g['name'] ?? ($genreMap[$g['id']] ?? '')];
                }
            }
        } elseif (!empty($data['genre_ids']) && is_array($data['genre_ids'])) {
            foreach ($data['genre_ids'] as $gid) {
                $gid = (int)$gid;
                $genres[] = ['id' => $gid, 'name' => $genreMap[$gid] ?? ''];
            }
        }

        $posterPath = isset($data['poster_path'])
            ? self::IMAGE_BASE . $data['poster_path']
            : null;

        $backdropPath = isset($data['backdrop_path'])
            ? self::IMAGE_BASE . $data['backdrop_path']
            : null;

        return new MovieVO(
            (int)($data['id'] ?? 0),
            (string)($data['title'] ?? ($data['original_title'] ?? '')),
            isset($data['overview']) ? (string)$data['overview'] : null,
            $posterPath,
            $backdropPath,
            isset($data['release_date']) ? (string)$data['release_date'] : null,
            $genres
        );
    }

    /**
     * Mapeia array de resposta de busca para array de MovieVO
     *
     * @param array $searchResponse
     * @param array<int,string> $genreMap
     * @return MovieVO[]
     */
    public static function fromSearchResponse(array $searchResponse, array $genreMap = []): array
    {
        $items = $searchResponse['results'] ?? [];
        if (!is_array($items)) {
            return [];
        }

        $movies = [];
        foreach ($items as $item) {
            $movies[] = self::fromApi($item, $genreMap);
        }

        return $movies;
    }
}
