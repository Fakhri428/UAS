<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeProgress extends Model
{
    protected $table = 'exchange_progress';

    protected $fillable = [
        'exchange_request_id',
        'user_id',
        'progress_note',
        'file_url',
    ];

    public function exchangeRequest()
    {
        return $this->belongsTo(ExchangeRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
