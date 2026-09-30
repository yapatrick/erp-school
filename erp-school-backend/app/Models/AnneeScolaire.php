<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ==========================================
// AnneeScolaire Model
// ==========================================
class AnneeScolaire extends Model
{
    protected $table = 'annees_scolaires';
    protected $primaryKey = 'id_annee_scolaire';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'en_cours'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'en_cours' => 'boolean'
    ];

    // Relations
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'id_annee_scolaire');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_annee_scolaire');
    }

    public function periodesEvaluation(): HasMany
    {
        return $this->hasMany(PeriodeEvaluation::class, 'id_annee_scolaire');
    }

    // Scope pour année en cours
    public function scopeEnCours($query)
    {
        return $query->where('en_cours', true);
    }
}
