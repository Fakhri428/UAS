<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_url',
        'project_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
