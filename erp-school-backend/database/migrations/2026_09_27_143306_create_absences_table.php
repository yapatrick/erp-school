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
        Schema::create('absences', function (Blueprint $table) {
            $table->id('id_absence');
            $table->foreignId('id_etudiant')
                  ->constrained('etudiants', 'id_etudiant')
                  ->cascadeOnDelete();
            $table->foreignId('id_enseignement')
                  ->constrained('enseignements', 'id_enseignement')
                  ->cascadeOnDelete();
            $table->foreignId('id_professeur')
                  ->constrained('professeurs', 'id_professeur')
                  ->cascadeOnDelete();
            $table->date('date_absence');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->enum('type_absence', ['Absence', 'Retard'])->nullable();
            $table->boolean('justifiee')->default(false);
            $table->string('motif', 500)->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();

            $table->index('id_etudiant', 'idx_absences_etudiant');
            $table->index('date_absence', 'idx_absences_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
