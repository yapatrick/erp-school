<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class UtilisateurPolicy
{
    /**
     * Liste des employés.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    /**
     * Voir un profil employé précis.
     */
    public function view(User $user, Utilisateur $utilisateur): bool
    {
        if (!$user->hasPermission('users.view')) {
            return false;
        }

        // Un employé peut toujours voir son propre profil
        if ($utilisateur->user_id === $user->id) {
            return true;
        }

        // Les autres nécessitent users.view (déjà vérifié) + rôle habilité
        return $user->hasAnyRole(['Admin', 'manager', 'Secrétaire']);
    }

    /**
     * Créer un profil employé.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    /**
     * Modifier un profil employé.
     */
    public function update(User $user, Utilisateur $utilisateur): bool
    {
        if (!$user->hasPermission('users.edit')) {
            return false;
        }

        // Un employé ne peut PAS modifier son propre salaire (sécurité RH)
        // → on l'autorise seulement s'il est Admin ou manager
        if ($utilisateur->user_id === $user->id && !$user->hasAnyRole(['Admin', 'manager'])) {
            return Response::deny("Vous ne pouvez pas modifier votre propre salaire.");
        }

        return true;
    }

    /**
     * Supprimer un profil employé.
     */
    public function delete(User $user, Utilisateur $utilisateur): bool
    {
        if (!$user->hasPermission('users.delete')) {
            return false;
        }

        // Interdiction de supprimer un profil lié à un Admin
        if ($utilisateur->user && $utilisateur->user->hasRole('Admin')) {
            return Response::deny("Impossible de supprimer le profil d'un administrateur.");
        }

        return true;
    }
}