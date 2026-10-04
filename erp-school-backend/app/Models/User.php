<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;   // ← AJOUTER

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'actif',
        'derniere_connexion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
            'derniere_connexion' => 'datetime',
        ];
    }

    // ===== Relations RBAC =====

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',   // FK de User dans la pivot
            'id_role'    // FK de Role dans la pivot
        )->withTimestamps();
    }

    // ===== Helpers RBAC =====

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    /**
     * Vérifie une permission via les rôles de l'utilisateur.
     */
    public function hasPermission(string $slug): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('slug', $slug))
            ->exists();
    }

    /**
     * Récupère tous les slugs de permissions via les rôles.
     */
    public function getAllPermissions(): array
    {
        return Permission::whereHas('roles.users', function ($q) {
                $q->where('users.id', $this->id);
            })
            ->pluck('slug')
            ->unique()
            ->values()
            ->toArray();
    }

    public function assignRole(string $slug): void
    {
        $role = Role::where('slug', $slug)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id_role]);
    }

    public function removeRole(string $slug): void
    {
        $role = Role::where('slug', $slug)->firstOrFail();
        $this->roles()->detach($role->id_role);
    }

    // ===== Relation métier =====

    public function employe()
    {
        return $this->hasOne(Utilisateur::class, 'user_id');
    }
}