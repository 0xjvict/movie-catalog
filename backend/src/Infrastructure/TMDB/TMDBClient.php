<?php declare(strict_types=1);

namespace Infrastructure\TMDB;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

final class TMDBClient
{
    private const BASE_URL = 'https://api.themoviedb.org/3';
    private Client $client;
    private string $bearerToken;
    private int $cacheTtl;

    /**
     * @param string|null $bearerToken
     * @param int|null $cacheTtlSeconds
     */
    public function __construct(string $bearerToken = null, ?int $cacheTtlSeconds = null)
    {
        $this->bearerToken = $bearerToken ?? (string)env('TMDB_BEARER_TOKEN', '');
        $this->cacheTtl = $cacheTtlSeconds ?? (int)env('TMDB_CACHE_TTL', 3600);

        if (empty($this->bearerToken)) {
            throw new InvalidArgumentException('TMDB Bearer Token é obrigatório.');
        }

        $this->client = new Client([
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ],
        ]);
    }

    /**
     * Wrapper Genérico para requisições GET
     *
     * @param string $uri
     * @param array $query
     * @return array
     */
    public function get(string $uri, array $query = []): array
    {
        $queryKey = http_build_query($query);
        $cacheKey = "tmdb:GET:" . md5($uri . '::' . $queryKey);
        $finalQuery = array_merge(['language' => 'pt-BR'], $query);

        try {
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = $this->client->get(self::BASE_URL . $uri, ['query' => $finalQuery]);
            $decoded = json_decode((string)$response->getBody(), true);

            Cache::put($cacheKey, $decoded, $this->cacheTtl);

            return $decoded ?? [];
        } catch (GuzzleException $e) {
            Log::error('TMDBClient GET error: ' . $e->getMessage(), ['uri' => $uri, 'query' => $query]);
            return [];
        } catch (Throwable $e) {
            Log::warning('Erro ao acessar cache TMDB: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Wrapper para busca de filmes.
     *
     * @param string $queryText
     * @param int $page
     * @return array
     */
    public function searchMovies(string $queryText, int $page = 1): array
    {
        if ($queryText === '') {
            return ['results' => [], 'page' => $page, 'total_results' => 0, 'total_pages' => 0];
        }

        return $this->get('/search/movie', [
            'query' => $queryText,
            'page' => $page,
            'include_adult' => 'false',
        ]);
    }

    /**
     * Wrapper para detalhes do filme.
     *
     * @param int $id
     * @return array
     */
    public function getMovieDetails(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        return $this->get("/movie/$id");
    }

    /**
     * Recupera a lista completa de gêneros (id => nome) com cache.
     *
     * @return array<int,string>
     */
    public function getGenresList(): array
    {
        $cacheKey = 'tmdb:genres:list';

        $fetchGenres = function (): array {
            $resp = $this->get('/genre/movie/list');
            $list = $resp['genres'] ?? [];
            $map = [];

            foreach ($list as $g) {
                if (isset($g['id'], $g['name'])) {
                    $map[(int)$g['id']] = (string)$g['name'];
                }
            }

            return $map;
        };

        try {
            return Cache::remember($cacheKey, $this->cacheTtl, $fetchGenres);
        } catch (Throwable $e) {
            Log::warning('Erro ao recuperar lista de gêneros TMDB: ' . $e->getMessage());
            return $fetchGenres();
        }
    }
}
