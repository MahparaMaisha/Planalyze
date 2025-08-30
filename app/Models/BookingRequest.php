<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $fillable = [
        'user_id',
        'planner_id',
        'title',
        'description',
        'event_date',
        'category',
        'price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }
}
