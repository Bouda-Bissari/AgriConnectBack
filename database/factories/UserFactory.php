<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_number' => fake()->unique()->numerify('228########'),
            'fullName' => fake()->unique()->name(),
            'phone_number_verified_at' => now(),
            // 'password' => static::$password ??= Hash::make('password'),
            'password' => Hash::make('11111111'), 
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */

     public function configure()
     {
         return $this->afterCreating(function (User $user) {
             // Récupérer un rôle aléatoire
             $role = Role::inRandomOrder()->first();
 
             // Attribuer le rôle à l'utilisateur
             $user->roles()->attach($role);
         });
     }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
