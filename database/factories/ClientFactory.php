<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '+39 '.fake()->numerify('3## ### ####'),
            'email' => fake()->unique()->safeEmail(),
            'tax_code' => strtoupper(fake()->unique()->bothify('??????##?##?###?')),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'notes' => fake()->optional(0.35)->sentence(),
        ];
    }
}
