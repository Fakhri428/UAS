<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentoringBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentoring_room_id',
        'user_id',
        'status',
        'scheduled_at',
        'duration_minutes',
        'price',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(MentoringRoom::class, 'mentoring_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
