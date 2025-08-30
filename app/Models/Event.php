<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  use HasFactory;
    protected $fillable = [
      'planner_id',
      'client_id',
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
    public function client(){
      return $this->belongsTo(User::class, 'client_id');
    }
}
