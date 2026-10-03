<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==========================================
// 2026_10_01_000004_create_user_roles_table.php
// Pivot: Users ↔ Rôles (attaché à "users", pas "utilisateurs")
// ==========================================

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_role')->constrained('roles', 'id_role')->onDelete('cascade');
            $table->timestamps();
            
            // Un user n'a qu'une fois un rôle
            $table->unique(['user_id', 'id_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
