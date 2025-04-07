<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slip extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'slips';
    protected $softDelete = true;
    protected $fillable = [
        'booking_id',
        'slip_image_url',
        'amount',
        'payment_status',
        'payment_date',
    ];
    protected $hidden = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
