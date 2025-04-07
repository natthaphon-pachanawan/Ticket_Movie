<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Movie extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'movies';
    protected $softDelete = true;
    protected $fillable = [
        'title',
        'description',
        'genre',
        'director',
        'cast',
        'duration',
        'release_date',
        'poster_url',
    ];
    protected $hidden = ['deleted_at'];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
