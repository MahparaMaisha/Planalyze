<?php

// database/seeders/PlannerSeeder.php
namespace Database\Seeders;

use App\Models\Planner;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get planner role
        $plannerRole = Role::where('name', 'planner')->first();

        // Get users with planner role
        $plannerUsers = User::where('role_id', $plannerRole->id)->get();

        // Create planner profiles for existing planner users
        foreach ($plannerUsers as $user) {
            if (!$user->planner) { // Only create if planner profile doesn't exist
                Planner::create([
                    'user_id' => $user->id,
                    'name' => $user->name . ' Events',
                    'bio' => $this->generatePlannerBio(),
                ]);
            }
        }

        // Create additional specialized planners
        Planner::factory()->count(5)->wedding()->create();
        Planner::factory()->count(3)->corporate()->create();
        Planner::factory()->count(4)->party()->create();
        Planner::factory()->count(8)->create(); // General planners
    }

    /**
     * Generate a realistic planner bio
     */
    private function generatePlannerBio(): string
    {
        $bios = [
            'With over 10 years of experience in event planning, I specialize in creating unforgettable experiences that exceed expectations. From intimate gatherings to grand celebrations, every detail matters.',
            'Passionate event planner dedicated to bringing your vision to life. I handle everything from concept to execution, ensuring your special day is stress-free and perfectly planned.',
            'Professional event coordinator with expertise in weddings, corporate events, and social gatherings. Known for creative solutions and impeccable attention to detail.',
            'Creative event designer who loves turning dreams into reality. Specializing in unique, personalized events that reflect your style and personality.',
            'Experienced event management professional committed to delivering exceptional service. I work closely with clients to create memorable experiences within budget.',
            'Award-winning event planner with a reputation for innovative designs and flawless execution. Your satisfaction is my top priority.',
        ];

        return $bios[array_rand($bios)];
    }
}
