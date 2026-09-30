<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            // ✅ id() génère BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->id('id_inscription');
            $table->foreignId('id_etudiant')
                  ->constrained('etudiants', 'id_etudiant')
                  ->cascadeOnDelete();
            $table->foreignId('id_classe')
                  ->constrained('classes', 'id_classe')
                  ->cascadeOnDelete();
            $table->foreignId('id_annee_scolaire')
                  ->constrained('annees_scolaires', 'id_annee_scolaire')
                  ->cascadeOnDelete();
            $table->dateTime('date_inscription')->useCurrent();
            $table->enum('type_inscription', ['Inscription', 'Réinscription'])->nullable();
            $table->enum('statut', ['En cours', 'Validée', 'Annulée', 'Terminée'])->nullable();
            $table->decimal('montant_total', 18, 2)->nullable();
            $table->decimal('montant_paye', 18, 2)->default(0);
            $table->decimal('solde', 18, 2)->storedAs('montant_total - montant_paye')->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();

            $table->index('id_etudiant', 'idx_inscriptions_etudiant');
            $table->index('statut', 'idx_inscriptions_statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};