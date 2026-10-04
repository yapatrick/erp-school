<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('notes.view');
    }

    public function view(User $user, Note $note): bool
    {
        return $user->hasPermission('notes.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('notes.create');
    }

    public function update(User $user, Note $note): bool
    {
        if (!$user->hasPermission('notes.edit')) {
            return false;
        }

        // Un professeur ne peut modifier que ses propres notes
        if ($user->hasRole('Professeur') && !$user->hasRole('Admin')) {
            return $note->professeur_id === $user->id;
        }

        return true;
    }

    public function delete(User $user, Note $note): bool
    {
        // Seul un Admin peut supprimer une note
        return $user->hasRole('Admin');
    }
}