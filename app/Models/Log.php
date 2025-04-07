<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'logs';
    protected $softDelete = true;
    protected $fillable = [
        'action',
        'description',
        'user_id',
        'ip_address',
    ];
    protected $hidden = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
