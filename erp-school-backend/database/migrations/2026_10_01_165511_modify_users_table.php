<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==========================================
// 2026_01_01_000005_modify_users_table.php
// Supprimer la colonne 'role' enum de users
// ==========================================

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la colonne enum 'role'
            $table->dropColumn('role');
            
            // Ajouter un flag pour les admins (optionnel, pour vérifications rapides)
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Caissier', 'Professeur', 'Secrétaire'])->default('Professeur');
            $table->dropColumn('is_admin');
        });
    }
};

