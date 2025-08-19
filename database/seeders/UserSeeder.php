<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $plannerRole = Role::where('name', 'planner')->first();
        $clientRole = Role::where('name', 'client')->first();



        // Create sample planner users
        User::firstOrCreate(
            ['email' => 'planner1@example.com'],
            [
                'name' => 'John Wedding Planner',
                'role_id' => $plannerRole->id,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'planner2@example.com'],
            [
                'name' => 'Sarah Event Organizer',
                'role_id' => $plannerRole->id,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Create sample client users
        User::firstOrCreate(
            ['email' => 'client1@example.com'],
            [
                'name' => 'Mike Johnson',
                'role_id' => $clientRole->id,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Emily Davis',
                'role_id' => $clientRole->id,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Create additional random users
        User::factory()->count(10)->planner()->create();
        User::factory()->count(20)->client()->create();
    }
}
