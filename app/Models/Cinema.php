<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cinemas';
    protected $softDelete = true;
    protected $fillable = [
        'name',
        'address',
        'province_id',
        'district_id',
        'subdistrict_id',
        'contact_phone',
        'contact_email',
    ];
    protected $hidden = ['deleted_at'];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id');
    }

    public function screeningRooms()
    {
        return $this->hasMany(Screening_Room::class, 'cinema_id');
    }
}
