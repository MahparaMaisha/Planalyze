<?php

// database/seeders/ReviewSeeder.php
namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get client and planner roles
        $clientRole = Role::where('name', 'client')->first();
        $plannerRole = Role::where('name', 'planner')->first();

        $clientUsers = User::where('role_id', $clientRole->id)->pluck('id')->toArray();
        $plannerUsers = User::where('role_id', $plannerRole->id)->pluck('id')->toArray();

        if (empty($clientUsers) || empty($plannerUsers)) {
            $this->command->warn('No client or planner users found. Please run UserSeeder first.');
            return;
        }

        // Create reviews with realistic distribution (more positive than negative)
        // 40% excellent reviews (5 stars)
        Review::factory()
            ->count(40)
            ->excellent()
            ->create([
                'user_id' => function () use ($clientUsers) {
                    return $clientUsers[array_rand($clientUsers)];
                },
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        // 5% poor reviews (1 star)
        Review::factory()
            ->count(5)
            ->poor()
            ->create([
                'user_id' => function () use ($clientUsers) {
                    return $clientUsers[array_rand($clientUsers)];
                },
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);

        // 55% random reviews (mixed ratings)
        Review::factory()
            ->count(55)
            ->create([
                'user_id' => function () use ($clientUsers) {
                    return $clientUsers[array_rand($clientUsers)];
                },
                'planner_id' => function () use ($plannerUsers) {
                    return $plannerUsers[array_rand($plannerUsers)];
                }
            ]);
    }
}
