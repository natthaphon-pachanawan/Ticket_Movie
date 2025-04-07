<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'provinces';
    protected $softDelete = true;
    protected $fillable = [
        'name_th',
        'name_en',
        'geography_id',
    ];
    protected $hidden = ['deleted_at'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function cinemas()
    {
        return $this->hasMany(Cinema::class);
    }
}
