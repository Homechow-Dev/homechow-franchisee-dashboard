<?php

namespace Database\Factories;

use App\Models\Kiosk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restock>
 */
class RestockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kiosk_id' => Kiosk::factory(),
            'restockId' => fake()->numerify('######'),
            'kioskName' => fake()->company(),
            'mealName' => fake()->streetAddress(),
            'category' => fake()->city(),
            'qty' => fake()->numberBetween(5, 24),
            'deliverName' => fake()->postcode(),
            'status' => fake()->randomElement($array = array ('Inprogress','Completed')),
        ];
    }
}
