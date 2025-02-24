<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
