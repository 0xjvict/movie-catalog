<?php

return [
    'api_url' => env('TMDB_API_URL', 'https://api.themoviedb.org/3'),
    'image_url' => env('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/w500'),
    'bearer_token' => env('TMDB_BEARER_TOKEN'),
    'cache_ttl' => env('TMDB_CACHE_TTL', 3600),
];
