<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified',
        'plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Hanya plan Pro Max yang boleh mengadakan kelas online (mentoring).
     */
    public function canHostClass(): bool
    {
        return $this->plan?->name === 'Pro Max';
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function needs()
    {
        return $this->hasMany(Need::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function mentoringRooms()
    {
        return $this->hasMany(MentoringRoom::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mentoringBookings()
    {
        return $this->hasMany(MentoringBooking::class);
    }
}
