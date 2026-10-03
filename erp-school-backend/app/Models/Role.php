<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'nomRole',
        'slug',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // ===== Relations =====

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'id_role',
            'id_permission'
        )->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'id_role',
            'user_id'   // adapte selon ta migration
        )->withTimestamps();
    }

    // ===== Méthodes =====

    public function hasPermission(string $slug): bool
    {
        return $this->permissions()
            ->where('slug', $slug)
            ->exists();
    }

    public function getAllPermissions()
    {
        return $this->permissions()->pluck('slug')->toArray();
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}