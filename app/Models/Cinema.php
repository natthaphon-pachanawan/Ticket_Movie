<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Cinema extends Model
{
    use HasFactory;
    use SoftDeletes;
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
}
