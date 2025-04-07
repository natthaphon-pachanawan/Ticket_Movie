<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screening_Room extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'screening_rooms';
    protected $softDelete = true;
    protected $fillable = [
        'cinema_id',
        'room_name',
        'seat_capacity',
        'description',
    ];
    protected $hidden = ['deleted_at'];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'screening_room_id');
    }

    public function screenings()
    {
        return $this->hasMany(Screening::class, 'screening_room_id');
    }
}
