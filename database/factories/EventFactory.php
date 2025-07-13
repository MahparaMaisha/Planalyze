<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'planner_id' => 1,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'category' => $this->faker->randomElement(['wedding', 'birthday', 'corporate', 'festival']),
            'price' => $this->faker->randomFloat(2, 1000, 10000),
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}
