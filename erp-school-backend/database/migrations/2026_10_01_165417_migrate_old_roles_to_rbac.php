<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==========================================
// 2024_01_01_000006_migrate_old_roles_to_rbac.php
// MIGRATION DES DONNÉES : Anciennes valeurs "role" → Nouveaux rôles
// ==========================================

return new class extends Migration
{
    public function up(): void
    {
        // Récupérer tous les users avec leur ancien rôle
        $users = \DB::table('users')
            ->select('id', 'role')
            ->where('role', '!=', null)
            ->get();

        // Mapping : ancien rôle → id du rôle (après le seeder)
        $roleMapping = [
            'Admin' => 1,
            'Caissier' => 2,
            'Professeur' => 3,
            'Secrétaire' => 4
        ];

        foreach ($users as $user) {
            $roleId = $roleMapping[$user->role] ?? null;

            if (!$roleId) {
                \Log::warning("Rôle non trouvé pour user {$user->id}: {$user->role}");
                continue;
            }

            // Assigner le rôle dans user_roles
            \DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'id_role' => $roleId
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Marquer les Admin avec is_admin = 1
            if ($user->role === 'Admin') {
                \DB::table('users')
                    ->where('id', $user->id)
                    ->update(['is_admin' => 1]);
            }
        }

        echo "Migration: " . count($users) . " users migrés vers RBAC\n";
    }

    public function down(): void
    {
        \DB::table('user_roles')->truncate();
        \DB::table('users')->update(['is_admin' => 0]);
    }
};

