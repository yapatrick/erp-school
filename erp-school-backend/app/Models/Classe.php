<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Classe extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'id_classe';

    protected $fillable = [
        'nom_classe',
        'niveau',
        'capacite_max',
        'frais_inscription',
        'frais_scolarite',
        'id_annee_scolaire',
        'active'
    ];

    protected $casts = [
        'capacite_max' => 'integer',
        'frais_inscription' => 'decimal:2',
        'frais_scolarite' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'id_annee_scolaire');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_classe');
    }

    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'id_classe');
    }

    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(
            Etudiant::class,
            'inscriptions',       // table pivot
            'id_classe',          // FK sur la pivot → vers classes
            'id_etudiant',        // FK sur la pivot → vers etudiants
            'id_classe',          // PK locale (classes)
            'id_etudiant'         // PK locale (etudiants)
        )
        ->wherePivot('statut', 'Validée')
        ->withPivot(['statut', 'date_inscription', 'id_inscription'])
        ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

