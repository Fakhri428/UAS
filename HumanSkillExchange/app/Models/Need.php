<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Need extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'description',
        'exchange_offer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
