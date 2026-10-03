<?php

namespace Database\Seeders;
// ==========================================
// Seeder : database/seeders/RbacSeeder.php
// ==========================================

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;


class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // ====== PERMISSIONS ======
        $permissions = [
            // Étudiants
            ['nom' => 'Voir étudiants', 'slug' => 'voir_etudiants', 'categorie' => 'etudiants'],
            ['nom' => 'Créer étudiant', 'slug' => 'creer_etudiant', 'categorie' => 'etudiants'],
            ['nom' => 'Modifier étudiant', 'slug' => 'modifier_etudiant', 'categorie' => 'etudiants'],
            ['nom' => 'Supprimer étudiant', 'slug' => 'supprimer_etudiant', 'categorie' => 'etudiants'],
            
            // Inscriptions
            ['nom' => 'Voir inscriptions', 'slug' => 'voir_inscriptions', 'categorie' => 'inscriptions'],
            ['nom' => 'Créer inscription', 'slug' => 'creer_inscription', 'categorie' => 'inscriptions'],
            ['nom' => 'Valider inscription', 'slug' => 'valider_inscription', 'categorie' => 'inscriptions'],
            ['nom' => 'Annuler inscription', 'slug' => 'annuler_inscription', 'categorie' => 'inscriptions'],
            
            // Paiements
            ['nom' => 'Voir paiements', 'slug' => 'voir_paiements', 'categorie' => 'paiements'],
            ['nom' => 'Enregistrer paiement', 'slug' => 'enregistrer_paiement', 'categorie' => 'paiements'],
            ['nom' => 'Modifier paiement', 'slug' => 'modifier_paiement', 'categorie' => 'paiements'],
            ['nom' => 'Voir rapport caisse', 'slug' => 'voir_rapport_caisse', 'categorie' => 'paiements'],
            
            // Notes
            ['nom' => 'Voir notes', 'slug' => 'voir_notes', 'categorie' => 'notes'],
            ['nom' => 'Ajouter note', 'slug' => 'ajouter_note', 'categorie' => 'notes'],
            ['nom' => 'Modifier note', 'slug' => 'modifier_note', 'categorie' => 'notes'],
            ['nom' => 'Voir bulletin', 'slug' => 'voir_bulletin', 'categorie' => 'notes'],
            
            // Absences
            ['nom' => 'Voir absences', 'slug' => 'voir_absences', 'categorie' => 'absences'],
            ['nom' => 'Enregistrer absence', 'slug' => 'enregistrer_absence', 'categorie' => 'absences'],
            ['nom' => 'Justifier absence', 'slug' => 'justifier_absence', 'categorie' => 'absences'],
            
            // Classes
            ['nom' => 'Voir classes', 'slug' => 'voir_classes', 'categorie' => 'classes'],
            ['nom' => 'Créer classe', 'slug' => 'creer_classe', 'categorie' => 'classes'],
            ['nom' => 'Modifier classe', 'slug' => 'modifier_classe', 'categorie' => 'classes'],
            
            // Admin
            ['nom' => 'Gérer utilisateurs', 'slug' => 'gerer_utilisateurs', 'categorie' => 'admin'],
            ['nom' => 'Gérer rôles', 'slug' => 'gerer_roles', 'categorie' => 'admin'],
            ['nom' => 'Gérer permissions', 'slug' => 'gerer_permissions', 'categorie' => 'admin'],
            ['nom' => 'Voir rapports', 'slug' => 'voir_rapports', 'categorie' => 'admin'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // ====== RÔLES ======
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['nomRole' => 'Administrateur', 'description' => 'Accès complet', 'actif' => true]
        );

        $caissier = Role::firstOrCreate(
            ['slug' => 'caissier'],
            ['nomRole' => 'Caissier', 'description' => 'Gestion paiements', 'actif' => true]
        );

        $professeur = Role::firstOrCreate(
            ['slug' => 'professeur'],
            ['nomRole' => 'Professeur', 'description' => 'Gestion notes/absences', 'actif' => true]
        );

        $secretaire = Role::firstOrCreate(
            ['slug' => 'secretaire'],
            ['nomRole' => 'Secrétaire', 'description' => 'Gestion administrative', 'actif' => true]
        );

        // ====== ASSIGNER PERMISSIONS AUX RÔLES ======
        $allPerms = Permission::all()->pluck('id_permission')->toArray();
        $admin->permissions()->sync($allPerms);

        $caissierPerms = Permission::whereIn('slug', [
            'voir_paiements', 'enregistrer_paiement', 'modifier_paiement', 
            'voir_rapport_caisse', 'voir_etudiants', 'voir_inscriptions'
        ])->pluck('id_permission')->toArray();
        $caissier->permissions()->sync($caissierPerms);

        $professeurPerms = Permission::whereIn('slug', [
            'voir_etudiants', 'voir_notes', 'ajouter_note', 'modifier_note',
            'voir_absences', 'enregistrer_absence', 'justifier_absence', 
            'voir_classes', 'voir_bulletin'
        ])->pluck('id_permission')->toArray();
        $professeur->permissions()->sync($professeurPerms);

        $secretairePerms = Permission::whereIn('slug', [
            'voir_etudiants', 'creer_etudiant', 'modifier_etudiant',
            'voir_inscriptions', 'creer_inscription', 'valider_inscription', 
            'annuler_inscription', 'voir_classes', 'creer_classe', 'modifier_classe'
        ])->pluck('id_permission')->toArray();
        $secretaire->permissions()->sync($secretairePerms);

        echo "✓ 30 permissions créées\n";
        echo "✓ 4 rôles créés\n";
        echo "✓ Permissions assignées aux rôles\n";
    }
}


