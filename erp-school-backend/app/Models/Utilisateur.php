<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

// ==========================================
// Utilisateur Model - PROFIL RH
// Reste inchangé (juste profil employé)
// ==========================================

class Utilisateur extends Model
{
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'user_id',      // FK vers users
        'matricule',
        'poste',
        'telephone',
        'adresse',
        'date_embauche',
        'salaire'
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'salaire' => 'decimal:2'
    ];

    protected $appends = ['nom_complet'];

    // ==========================================
    // RELATIONS
    // ==========================================

    /**
     * Relation 1-to-1 inverse avec User (authentification)
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================
    // ACCESSEURS
    // ==========================================

    public function getNomCompletAttribute(): string
    {
        // Récupérer nom/prenom depuis... (tu dois m'indiquer d'où)
        // Pour l'instant, construire depuis les données disponibles
        return $this->poste ?: 'Employé';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActif($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('actif', true);
        });
    }

    public function scopeParPoste($query, $poste)
    {
        return $query->where('poste', $poste);
    }
}


