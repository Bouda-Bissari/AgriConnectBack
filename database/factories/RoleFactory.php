<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Cette méthode ne sera pas utilisée car nous ne créons pas des rôles aléatoires ici.
        ];
    }

    /**
     * Create specific roles.
     *
     * @return void
     */
    public static function createRoles()
    {
        $roles = ['demandeur', 'postulant', 'admin', 'moderateur'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
