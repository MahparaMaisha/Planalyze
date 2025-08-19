<?php

// database/seeders/EventSeeder.php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get planner role and users
        $plannerRole = Role::where('name', 'planner')->first();
        $plannerUsers = User::where('role_id', $plannerRole->id)->pluck('id')->toArray();

        if (empty($plannerUsers)) {
            $this->command->warn('No planner users found. Please run UserSeeder first.');
            return;
        }

        // Create various types of events
        Event::factory()
            ->count(15)
            ->wedding()
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        Event::factory()
            ->count(12)
            ->corporate()
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        Event::factory()
            ->count(20)
            ->birthday()
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        // Create completed events
        Event::factory()
            ->count(25)
            ->completed()
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        // Create upcoming active events
        Event::factory()
            ->count(30)
            ->active()
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        // Create random events
        Event::factory()
            ->count(40)
            ->create([
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);
    }
}
