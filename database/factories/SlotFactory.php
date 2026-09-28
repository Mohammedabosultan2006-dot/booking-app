<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+7 days');

        return [
            'service_id' => Service::factory(),
            'start_at'   => $start,
            'end_at'     => (clone $start)->modify('+30 minutes'),
            'is_booked'  => false,
        ];
    }
}
