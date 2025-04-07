<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users';
    protected $softDelete = true;
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
        'phone_number',
        'gender',
        'role_id',
    ];
    protected $hidden = ['password', 'deleted_at'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
