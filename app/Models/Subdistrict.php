<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdistrict extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'subdistricts';
    protected $softDelete = true;
    protected $fillable = [
        'district_id',
        'name_th',
        'name_en',
        'zipcode',
    ];
    protected $hidden = ['deleted_at'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function cinemas()
    {
        return $this->hasMany(Cinema::class);
    }
}
