<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Utilisateur extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'login',
        'mot_de_passe',
        'nom',
        'prenom',
        'role',
        'email',
        'actif',
        'derniere_connexion'
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'derniere_connexion' => 'datetime'
    ];

    // Pour JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'nom' => $this->nom,
            'prenom' => $this->prenom
        ];
    }

    // Override pour utiliser mot_de_passe au lieu de password
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function paiementsEnregistres(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_utilisateur_caisse');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParRole($query, $role)
    {
        return $query->where('role', $role);
    }
}

