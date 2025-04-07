<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tickets';
    protected $softDelete = true;
    protected $fillable = [
        'booking_id',
        'screening_id',
        'seat_id',
        'ticket_code',
        'price',
        'status',
        'issued_at',
    ];
    protected $hidden = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
