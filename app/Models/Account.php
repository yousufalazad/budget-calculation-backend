<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
