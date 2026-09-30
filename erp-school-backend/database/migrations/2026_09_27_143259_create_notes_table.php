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
        Schema::create('notes', function (Blueprint $table) {
            $table->id('id_note');
            $table->foreignId('id_etudiant')
                  ->constrained('etudiants', 'id_etudiant')
                  ->cascadeOnDelete();
            $table->foreignId('id_matiere')
                  ->constrained('matieres', 'id_matiere')
                  ->cascadeOnDelete();
            $table->foreignId('id_professeur')
                  ->constrained('professeurs', 'id_professeur')
                  ->cascadeOnDelete();
            $table->foreignId('id_periode')
                  ->constrained('periodes_evaluation', 'id_periode')
                  ->cascadeOnDelete();
            $table->enum('type_evaluation', ['Devoir', 'Interrogation', 'Examen', 'Composition'])->nullable();
            $table->decimal('note', 5, 2);
            $table->decimal('note_sur', 5, 2)->default(20);
            $table->date('date_evaluation')->useCurrent();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('id_etudiant', 'idx_notes_etudiant');
            $table->index('id_periode', 'idx_notes_periode');
        });

       // DB::statement('ALTER TABLE notes ADD CONSTRAINT chk_note_valide CHECK (note >= 0 AND note <= note_sur)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
