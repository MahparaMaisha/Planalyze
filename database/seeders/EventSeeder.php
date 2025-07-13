<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    public function run()
    {
        // Ensure some planners exist
        $planners = User::where('role', 'planner')->get();

        if ($planners->isEmpty()) {
            User::factory()->count(5)->create(['role' => 'planner']);
            $planners = User::where('role', 'planner')->get();
        }

        foreach ($planners as $planner) {
            Event::factory()->count(3)->create([
                'planner_id' => $planner->id
            ]);
        }
    }
}
