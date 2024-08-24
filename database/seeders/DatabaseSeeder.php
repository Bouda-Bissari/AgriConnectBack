<?php

namespace Database\Seeders;

use App\Models\Candidature;
use App\Models\Detail;
use App\Models\Service;
use App\Models\User;
use Database\Factories\RoleFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // RoleFactory::createRoles();

        User::factory(20)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Detail::factory(20)->create();
        Service::factory(20)->create();

        Candidature::factory(20)->create();

    }
}
