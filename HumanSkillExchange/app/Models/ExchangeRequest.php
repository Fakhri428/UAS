<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'offer_id',
        'need_id',
        'message',
        'status',
        'completed_by_from_user',
        'completed_by_to_user',
    ];

    protected function casts(): array
    {
        return [
            'completed_by_from_user' => 'boolean',
            'completed_by_to_user' => 'boolean',
        ];
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function need()
    {
        return $this->belongsTo(Need::class);
    }
}
