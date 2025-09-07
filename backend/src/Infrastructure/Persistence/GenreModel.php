<?php declare(strict_types=1);

namespace Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class GenreModel extends Model
{
    protected $table = 'genres';
    public $incrementing = false; // ID vem da API da TMDB
    protected $fillable = ['id', 'name'];

    public function favorites()
    {
        return $this->belongsToMany(
            FavoriteModel::class,
            'favorite_genre',
            'genre_id',
            'favorite_id'
        );
    }
}
