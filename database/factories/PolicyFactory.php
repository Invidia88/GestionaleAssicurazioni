<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Policy>
 */
class PolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = fake()->dateTimeBetween('now', '+1 year');

        return [
            'client_id' => Client::factory(),
            'insurance_company_id' => InsuranceCompany::factory(),
            'type' => fake()->randomElement(Policy::TYPES),
            'number' => strtoupper(fake()->unique()->bothify('POL-####-??')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'annual_premium' => fake()->randomFloat(2, 180, 2400),
            'status' => fake()->randomElement(array_keys(Policy::STATUSES)),
            'notes' => fake()->optional(0.35)->sentence(),
        ];
    }
}
