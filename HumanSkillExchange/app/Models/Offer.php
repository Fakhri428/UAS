<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'category',
        'description',
        'exchange_expectation',
        'available_duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
