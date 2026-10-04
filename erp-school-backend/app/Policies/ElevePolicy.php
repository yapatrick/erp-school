<?php

namespace App\Policies;

use App\Models\Eleve;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ElevePolicy
{
    /**
     * L'utilisateur peut-il voir la liste des élèves ?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('eleves.view');
    }

    /**
     * L'utilisateur peut-il voir cet élève en particulier ?
     */
    public function view(User $user, Eleve $eleve): bool
    {
        if (!$user->hasPermission('eleves.view')) {
            return false;
        }

        // Exemple métier : un Professeur ne voit que les élèves de ses classes
        if ($user->hasRole('Professeur')) {
            return $eleve->classes()
                ->whereHas('professeurs', fn ($q) => $q->where('user_id', $user->id))
                ->exists();
        }

        return true;
    }

    /**
     * L'utilisateur peut-il créer un élève ?
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('eleves.create');
    }

    /**
     * L'utilisateur peut-il modifier cet élève ?
     */
    public function update(User $user, Eleve $eleve): bool
    {
        if (!$user->hasPermission('eleves.edit')) {
            return false;
        }

        // Exemple : un élève ne peut pas être modifié s'il est déjà diplômé/parti
        if ($eleve->statut === 'diplome') {
            return Response::deny("Impossible de modifier un élève diplômé.");
        }

        return true;
    }

    /**
     * L'utilisateur peut-il supprimer cet élève ?
     */
    public function delete(User $user, Eleve $eleve): bool
    {
        if (!$user->hasPermission('eleves.delete')) {
            return false;
        }

        // Exemple : interdiction si l'élève a des paiements enregistrés
        if ($eleve->paiements()->exists()) {
            return Response::deny("Suppression impossible : paiements associés.");
        }

        return true;
    }

    /**
     * Restaurer un élève supprimé (soft delete).
     */
    public function restore(User $user, Eleve $eleve): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Supprimer définitivement.
     */
    public function forceDelete(User $user, Eleve $eleve): bool
    {
        return $user->hasRole('Admin');
    }
}