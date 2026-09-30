<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            // ✅ id() → BIGINT UNSIGNED
            $table->id('id_paiement');
            $table->foreignId('id_etudiant')
                  ->constrained('etudiants', 'id_etudiant')
                  ->cascadeOnDelete();
            // ✅ nullable() DOIT être appelé AVANT constrained()
            $table->foreignId('id_inscription')
                  ->nullable()
                  ->constrained('inscriptions', 'id_inscription')
                  ->nullOnDelete();
            $table->foreignId('id_type_paiement')
                  ->constrained('types_paiement', 'id_type')
                  ->restrictOnDelete();
            $table->decimal('montant', 18, 2);
            $table->dateTime('date_paiement')->useCurrent();
            $table->enum('mode_paiement', ['Espèces', 'Chèque', 'Virement', 'Mobile Money'])->nullable();
            $table->string('numero_recu', 50)->unique();
            $table->string('reference', 100)->nullable();
            $table->foreignId('id_utilisateur_caisse')
                  ->nullable()
                  ->constrained('utilisateurs', 'id_utilisateur')
                  ->nullOnDelete();
            $table->text('remarques')->nullable();
            $table->timestamps();

            $table->index('id_etudiant', 'idx_paiements_etudiant');
            $table->index('date_paiement', 'idx_paiements_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};