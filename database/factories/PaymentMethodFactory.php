<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id'    => User::factory()->state(['role' => 'provider']),
            'type'           => 'bank',
            'label'          => 'بنك فلسطين',
            'account_name'   => fake()->name(),
            'account_number' => 'PS' . fake()->numerify('########') . 'PALS',
            'is_active'      => true,
        ];
    }
}
