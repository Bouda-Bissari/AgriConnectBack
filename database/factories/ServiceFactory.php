<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
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
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph, 
            'service_type' => $this->faker->randomElement(['work', 'material']), 
            'deadline' => $this->faker->dateTimeBetween('now', '+1 year'), 
            'user_id' => User::inRandomOrder()->first()->id, 
        ];
    }
}
