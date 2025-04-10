<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'screenings';
    protected $softDelete = true;
    protected $fillable = [
        'movie_id',
        'screening_room_id',
        'screening_datetime',
        'price',
    ];
    protected $hidden = ['deleted_at'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function screeningRoom()
    {
        return $this->belongsTo(ScreeningRoom::class, 'screening_room_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
