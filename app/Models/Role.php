<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'roles';
    protected $softDelete = true;
    protected $fillable = [
        'role_name',
        'role_description',
    ];
    protected $hidden = ['deleted_at'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
