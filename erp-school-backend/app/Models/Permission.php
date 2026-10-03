<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


// ==========================================
// Permission Model
// ==========================================

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id_permission';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'categorie',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'id_permission',
            'id_role'
        )->withTimestamps();
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }
}

