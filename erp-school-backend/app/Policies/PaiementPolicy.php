<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaiementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('paiements.view');
    }

    public function view(User $user, Paiement $paiement): bool
    {
        if (!$user->hasPermission('paiements.view')) {
            return false;
        }

        // Un Caissier ne voit que les paiements qu'il a encaissés
        if ($user->hasRole('Caissier') && !$user->hasRole('manager')) {
            return $paiement->user_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('paiements.create');
    }

    public function update(User $user, Paiement $paiement): bool
    {
        // Un paiement validé ne peut plus être modifié (sauf Admin)
        if ($paiement->statut === 'valide' && !$user->hasRole('Admin')) {
            return Response::deny("Un paiement validé ne peut plus être modifié.");
        }

        return $user->hasPermission('paiements.create')
            || $user->hasPermission('paiements.validate');
    }

    /**
     * Action spécifique : valider un paiement.
     */
    public function validate(User $user, Paiement $paiement): bool
    {
        if (!$user->hasPermission('paiements.validate')) {
            return false;
        }

        // Un caissier ne peut pas valider son propre paiement (séparation des tâches)
        if ($paiement->user_id === $user->id && !$user->hasRole('Admin')) {
            return Response::deny("Vous ne pouvez pas valider votre propre paiement.");
        }

        return true;
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        // Seul un Admin peut supprimer un paiement
        if (!$user->hasRole('Admin')) {
            return Response::deny("Seul un administrateur peut supprimer un paiement.");
        }

        return true;
    }
}