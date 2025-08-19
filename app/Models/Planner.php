<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planner extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'bio'];
    /**
     * Get the user that owns the planner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
