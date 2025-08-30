<?php

// database/factories/EventFactory.php
namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['wedding', 'birthday', 'corporate', 'conference', 'graduation', 'anniversary', 'baby_shower', 'engagement'];

        return [
            'planner_id' => User::factory()->planner(),
            'client_id' => User::factory()->client(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(5),
            'event_date' => $this->faker->dateTimeBetween('-6 months', '+1 year'),
            'category' => $this->faker->randomElement($categories),
            'price' => $this->faker->randomFloat(2, 500, 50000),
            'status' => $this->faker->randomElement(['completed', 'active']),
        ];
    }

    /**
     * Create a completed event.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'event_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    /**
     * Create an active/upcoming event.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'event_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Create a wedding event.
     */
    public function wedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'wedding',
            'title' => $this->faker->firstName() . ' & ' . $this->faker->firstName() . ' Wedding',
            'description' => 'A beautiful wedding ceremony and reception with elegant decorations, professional photography, delicious catering, and memorable entertainment for all guests.',
            'price' => $this->faker->randomFloat(2, 15000, 75000),
        ]);
    }

    /**
     * Create a corporate event.
     */
    public function corporate(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'corporate',
            'title' => $this->faker->company() . ' ' . $this->faker->randomElement(['Annual Conference', 'Product Launch', 'Team Building', 'Awards Ceremony']),
            'description' => 'Professional corporate event with comprehensive planning including venue booking, catering, audio-visual setup, and coordination of all activities.',
            'price' => $this->faker->randomFloat(2, 5000, 30000),
        ]);
    }

    /**
     * Create a birthday party event.
     */
    public function birthday(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'birthday',
            'title' => $this->faker->firstName() . '\'s Birthday Celebration',
            'description' => 'Fun-filled birthday party with themed decorations, entertainment, catering, and special arrangements to make the birthday person feel extra special.',
            'price' => $this->faker->randomFloat(2, 1000, 8000),
        ]);
    }
}
