<?php

namespace Database\Factories;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Enums\ActivityVisibility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->addDays(rand(1, 30));
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => $start,
            'end_time' => $start->copy()->addHours(2),
            'location' => fake()->address(),
            'capacity' => 50,
            'qr_version' => 1,
            'is_registration_required' => true,
        ];
    }
}
