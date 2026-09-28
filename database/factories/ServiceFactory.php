<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id'      => User::factory()->state(['role' => 'provider']),
            'name'             => fake()->randomElement(['كشف عام', 'كشف أسنان', 'كشف أطفال']),
            'description'      => fake()->sentence(),
            'duration_minutes' => 30,
            'price'            => fake()->randomElement([0, 40, 60, 100]),
            'location'         => 'غزة',
            'is_active'        => true,
        ];
    }
}
