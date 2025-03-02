<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'account_id',
        'title',
        'type',
        'amount',
        'recurring_type_id',
        'start_date',
        'notes',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function recurringType()
    {
        return $this->belongsTo(RecurringType::class);
    }
}
