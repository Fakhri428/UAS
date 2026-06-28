<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentoringRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'title',
        'description',
        'schedule',
        'duration_minutes',
        'price',
        'status',
        'video_link',
        'meeting_notes',
    ];

    protected $casts = [
        'schedule' => 'datetime',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function bookings()
    {
        return $this->hasMany(MentoringBooking::class);
    }
}
