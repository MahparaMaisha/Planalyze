<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
      'planner_id',
      'title',
      'description',
      'event_date',
      'category',
      'price',
      'status',
    ];
    public function planner(){
      return $this->belongsTo(User::class, 'planner_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
