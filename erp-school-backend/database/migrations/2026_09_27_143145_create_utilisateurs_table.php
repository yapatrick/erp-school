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
        // utilisateurs = profil RH employé
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id('id_utilisateur');
            $table->foreignId('user_id')
                ->unique()                                // relation 1-1
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('matricule', 50)->unique();
            $table->string('poste', 100)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse', 500)->nullable();
            $table->date('date_embauche')->nullable();
            $table->decimal('salaire', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
