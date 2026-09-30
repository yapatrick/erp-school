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
        Schema::create('classes', function (Blueprint $table) {
            $table->id('id_classe');
            $table->string('nom_classe', 100);
            $table->string('niveau', 50);
            $table->integer('capacite_max')->nullable();
            $table->decimal('frais_inscription', 18, 2)->nullable();
            $table->decimal('frais_scolarite', 18, 2)->nullable();
            $table->foreignId('id_annee_scolaire')
                  ->constrained('annees_scolaires', 'id_annee_scolaire')
                  ->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
