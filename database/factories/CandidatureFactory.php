<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidature>
 */
class CandidatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            // 'user_id' => User::factory(),
            'service_id' => Service::inRandomOrder()->first()->id,
            'message' => $this->faker->paragraph,
            // 'status' => $this->faker->boolean,
            'status' => $this->faker->randomElement([
                'pending', 'accepted', 'rejected','canceled','deleted'
            ]),

        ];
    }
}
