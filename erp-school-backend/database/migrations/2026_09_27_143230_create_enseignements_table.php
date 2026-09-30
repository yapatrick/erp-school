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
        Schema::create('enseignements', function (Blueprint $table) {
            $table->id('id_enseignement');
            $table->foreignId('id_classe')
                  ->constrained('classes', 'id_classe')
                  ->cascadeOnDelete();
            $table->foreignId('id_matiere')
                  ->constrained('matieres', 'id_matiere')
                  ->cascadeOnDelete();
            $table->foreignId('id_professeur')
                  ->constrained('professeurs', 'id_professeur')
                  ->cascadeOnDelete();
            $table->integer('heures_semaine')->nullable();
            $table->string('jour_semaine', 20)->nullable();
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->string('salle', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignements');
    }
};
