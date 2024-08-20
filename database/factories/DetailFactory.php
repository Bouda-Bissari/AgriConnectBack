<?php

namespace Database\Factories;

use Alirezasedghi\LaravelImageFaker\Facades\ImageFaker;
use Alirezasedghi\LaravelImageFaker\ImageFaker as LaravelImageFakerImageFaker;
use Alirezasedghi\LaravelImageFaker\Services\Picsum;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Detail>
 */
class DetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtenir un utilisateur aléatoire
        $userId = User::inRandomOrder()->first()->id;


        return [
            'user_id' => $userId,


            'email' => $this->faker->unique()->safeEmail(),
            // 'age' => $this->faker->numberBetween(18, 99),
            'date' => $this->faker->dateTimeBetween('now', '+1 year'), 
            'gender' => $this->faker->randomElement(['Masculin', 'Feminin']),
            'image' => 'https://picsum.photos/200/300',
            'bio' => $this->faker->paragraph(),
            'company_name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'domaine' => $this->faker->randomElement([
                'Culture de céréales',
                'Culture de légumes',
                'Culture de fruits',
                'Culture de tubercules',
                'Élevage de bétail',
                'Élevage de volailles',
                'Aquaculture',
                'Jardinage',
                'Sarclage',
                'Arboriculture',
                'Horticulture',
                'Viticulture',
                'Culture de plantes médicinales',
                'Cultures sous serre',
                'Agroforesterie',
                'Culture bio',
                'Recrutement de travailleurs agricoles',
                'Gestion des fermes',
                'Consultation en agriculture',
                'Vente de produits agricoles',
                'Formation agricole',
            ]),
        ];
    }
}
