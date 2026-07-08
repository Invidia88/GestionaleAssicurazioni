<?php

namespace Database\Factories;

use App\Models\InsuranceCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InsuranceCompany>
 */
class InsuranceCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Assicurazioni',
            'contact_name' => fake()->name(),
            'contact_phone' => '+39 '.fake()->numerify('0# #######'),
            'contact_email' => fake()->unique()->companyEmail(),
            'notes' => fake()->optional(0.35)->sentence(),
        ];
    }
}
