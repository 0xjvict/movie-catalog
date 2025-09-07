<?php declare(strict_types=1);

namespace Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class FavoriteModel extends Model
{
    protected $table = 'favorites';
    protected $fillable = [
        'tmdb_id',
        'user_id',
        'title',
        'poster_path',
        'backdrop_path',
        'release_date',
        'origin_country',
        'runtime_minutes',
        'tagline',
        'overview',
        'vote_average',
        'vote_count'
    ];

    public function genres()
    {
        return $this->belongsToMany(
            GenreModel::class,
            'favorite_genre',
            'favorite_id',
            'genre_id'
        );
    }
}

