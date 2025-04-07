<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'districts';
    protected $softDelete = true;
    protected $fillable = [
        'province_id',
        'name_th',
        'name_en',
    ];
    protected $hidden = ['deleted_at'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function subdistricts()
    {
        return $this->hasMany(Subdistrict::class);
    }

    public function cinemas()
    {
        return $this->hasMany(Cinema::class);
    }
}
