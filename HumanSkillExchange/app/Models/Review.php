<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'exchange_request_id',
        'reviewer_id',
        'reviewed_user_id',
        'rating',
        'comment',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewedUser()
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    protected static function booted()
    {
        static::saved(function ($review) {
            if ($review->reviewee) {
                $review->reviewee->recalculateKoukanScore();
            }
        });

        static::deleted(function ($review) {
            if ($review->reviewee) {
                $review->reviewee->recalculateKoukanScore();
            }
        });
    }
}
