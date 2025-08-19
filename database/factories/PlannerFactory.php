<?php

// database/factories/PlannerFactory.php
namespace Database\Factories;

use App\Models\Planner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Planner>
 */
class PlannerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Planner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->planner(),
            'name' => $this->faker->company() . ' Events',
            'bio' => $this->faker->paragraph(3),
        ];
    }

    /**
     * Create planner with specific specialization.
     */
    public function wedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->company() . ' Wedding Planners',
            'bio' => 'Specializing in creating magical wedding experiences with attention to every detail. From intimate ceremonies to grand celebrations, we make your special day unforgettable.',
        ]);
    }

    /**
     * Create planner with corporate specialization.
     */
    public function corporate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->company() . ' Corporate Events',
            'bio' => 'Professional corporate event planning services including conferences, seminars, product launches, and team building activities. We deliver seamless business events.',
        ]);
    }

    /**
     * Create planner with party specialization.
     */
    public function party(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->company() . ' Party Planners',
            'bio' => 'Creating unforgettable celebrations for birthdays, anniversaries, graduations and all of life\'s special moments. Fun, creative, and stress-free party planning.',
        ]);
    }
}
