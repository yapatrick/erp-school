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
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id('id_annee_scolaire');
            $table->string('libelle', 50)->unique();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->boolean('en_cours')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // CHECK constraint (MySQL 8.0.16+)
           // DB::statement('ALTER TABLE annees_scolaires ADD CONSTRAINT chk_dates_annee CHECK (date_fin > date_debut)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annees_scolaires');
    }
};
