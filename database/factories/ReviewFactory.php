<?php

// database/factories/ReviewFactory.php
namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = $this->faker->numberBetween(1, 5);

        $comments = [
            1 => [
                'Very disappointed with the service.',
                'Poor quality and unprofessional.',
                'Would not recommend.',
                'Terrible experience overall.'
            ],
            2 => [
                'Below expectations, several issues.',
                'Service was okay but had problems.',
                'Could be much better.',
                'Not satisfied with the outcome.'
            ],
            3 => [
                'Average service, nothing special.',
                'It was okay, met basic expectations.',
                'Decent but room for improvement.',
                'Standard service, not bad but not great.'
            ],
            4 => [
                'Good service, mostly satisfied.',
                'Well organized with minor issues.',
                'Pretty good experience overall.',
                'Would likely use again.'
            ],
            5 => [
                'Excellent service! Highly recommend!',
                'Amazing work, exceeded expectations!',
                'Perfect planning, everything went smoothly!',
                'Outstanding professional service!',
                'Absolutely fantastic experience!'
            ]
        ];

        return [
            'user_id' => User::factory()->client(),
            'planner_id' => User::factory()->planner(),
            'rating' => $rating,
            'comment' => $this->faker->randomElement($comments[$rating]),
        ];
    }

    /**
     * Create a 5-star review.
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
            'comment' => $this->faker->randomElement([
                'Absolutely amazing! The planner exceeded all expectations and made our event perfect!',
                'Outstanding service from start to finish. Highly professional and creative!',
                'Best event planner ever! Everything was flawless and beautifully executed!',
                'Incredible attention to detail. Made our special day absolutely magical!'
            ]),
        ]);
    }

    /**
     * Create a 1-star review.
     */
    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 1,
            'comment' => $this->faker->randomElement([
                'Very disappointing experience. Poor communication and subpar results.',
                'Would not recommend. Many issues and unprofessional service.',
                'Terrible planning, nothing went as promised.',
                'Waste of money. Very unsatisfied with the service.'
            ]),
        ]);
    }
}
