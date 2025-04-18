<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'seats';
    protected $softDelete = true;
    protected $fillable = [
        'screening_room_id',
        'seat_number',
        'row',
        'column',
        'seat_type',
        'is_active',
        'is_reserved',
    ];
    protected $hidden = ['deleted_at'];

    public function screeningRoom()
    {
        return $this->belongsTo(ScreeningRoom::class, 'screening_room_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
