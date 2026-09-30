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
        Schema::create('periodes_evaluation', function (Blueprint $table) {
            $table->id('id_periode');
            $table->foreignId('id_annee_scolaire')
                  ->constrained('annees_scolaires', 'id_annee_scolaire')
                  ->cascadeOnDelete();
            $table->string('libelle', 100);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('ordre')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

       // DB::statement('ALTER TABLE periodes_evaluation ADD CONSTRAINT chk_dates_periode CHECK (date_fin > date_debut)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes_evaluation');
    }
};
