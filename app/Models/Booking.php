<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bookings';
    protected $softDelete = true;
    protected $fillable = [
        'user_id',
        'screening_id',
        'booking_datetime',
        'total_price',
        'status',
        'cancellation_reason',
    ];
    protected $hidden = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
