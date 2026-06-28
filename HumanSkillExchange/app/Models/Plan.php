<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'max_skills',
        'max_needs',
        'max_offers',
        'max_exchange_requests',
    ];
}
