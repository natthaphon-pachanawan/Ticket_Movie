<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

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
    protected $softCascade = ['seats'];

    protected static function booted()
    {
        static::deleting(function ($booking) {
            if ($booking->isForceDeleting()) {
                // ลบจริง
                $booking->seats()->detach();
            } else {
                // soft-delete: ลบ pivot ชั่วคราว
                $booking->seats()->detach();
            }
        });

        static::restoring(function ($booking) {
            // ถ้าต้อง restore ที่นั่งด้วย สามารถเขียน logic ตรงนี้
        });
    }

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

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seats');
    }
}
