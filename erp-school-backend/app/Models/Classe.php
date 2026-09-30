<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function etudiants()
    {
        return $this->hasManyThrough(
            Etudiant::class,
            Inscription::class,
            'id_classe',
            'id_etudiant',
            'id_classe',
            'id_etudiant'
        )->where('inscriptions.statut', 'Validée');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

