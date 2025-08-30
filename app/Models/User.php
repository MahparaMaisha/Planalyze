<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    // Reviews received by this planner (if reviews are directly on planners)
    public function reviews()
    {
        return $this->hasMany(Review::class, 'planner_id');
    }

    // Reviews given by this user (if they leave reviews)
    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    // Events planned by this user
    public function events()
    {
        return $this->hasMany(Event::class, 'planner_id');
    }

    // Planner profile
    public function planner()
    {
        return $this->hasOne(Planner::class);
    }

    public function clientEvents()
    {
        return $this->hasMany(Event::class, 'client_id');
    }

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'user_id');
    }
    public function bookingsAsPlanner()
    {
        return $this->hasMany(BookingRequest::class, 'planner_id');
    }
}
