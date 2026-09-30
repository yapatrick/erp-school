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
        DB::statement('ALTER TABLE annees_scolaires 
                   ADD CONSTRAINT chk_dates_annee 
                   CHECK (date_fin > date_debut)');

        DB::statement('ALTER TABLE periodes_evaluation 
                   ADD CONSTRAINT chk_dates_periode 
                   CHECK (date_fin > date_debut)');

        DB::statement('ALTER TABLE notes 
                   ADD CONSTRAINT chk_note_valide 
                   CHECK (note >= 0 AND note <= note_sur)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE annees_scolaires DROP CONSTRAINT chk_dates_annee');
        DB::statement('ALTER TABLE periodes_evaluation DROP CONSTRAINT chk_dates_periode');
         DB::statement('ALTER TABLE notes DROP CONSTRAINT chk_note_valide');
    
    }
};
