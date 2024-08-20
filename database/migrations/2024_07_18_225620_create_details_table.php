<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email')->unique()->nullable();
            // $table->integer('age')->nullable(); 
            $table->date('date')->nullable();
            $table->enum('gender', ['Masculin', 'Feminin']);
            $table->string('image')->nullable();
            $table->text('bio')->nullable(); 
            $table->string('address')->nullable();
            $table->enum('domaine', [
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
            ]);         
               $table->string('company_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details');
    }
};
