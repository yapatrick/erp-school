<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Etudiant extends Model
{
    protected $table = 'etudiants';
    protected $primaryKey = 'id_etudiant';

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'telephone',
        'email',
        'adresse',
        'nom_parent',
        'telephone_parent',
        'photo',
        'actif'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'actif' => 'boolean'
    ];

    protected $appends = ['nom_complet', 'age'];

    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }

    // Relations
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_etudiant');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_etudiant');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'id_etudiant');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'id_etudiant');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParClasse($query, $classeId)
    {
        return $query->whereHas('inscriptions', function($q) use ($classeId) {
            $q->where('id_classe', $classeId)
              ->where('statut', 'Validée');
        });
    }
}

