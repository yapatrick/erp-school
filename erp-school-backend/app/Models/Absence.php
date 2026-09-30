<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// Absence Model
// ==========================================
class Absence extends Model
{
    protected $table = 'absences';
    protected $primaryKey = 'id_absence';

    protected $fillable = [
        'id_etudiant',
        'id_enseignement',
        'id_professeur',
        'date_absence',
        'heure_debut',
        'heure_fin',
        'type_absence',
        'justifiee',
        'motif',
        'remarques'
    ];

    protected $casts = [
        'date_absence' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'justifiee' => 'boolean'
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function enseignement(): BelongsTo
    {
        return $this->belongsTo(Enseignement::class, 'id_enseignement');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function scopeNonJustifiee($query)
    {
        return $query->where('justifiee', false);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_absence', [$dateDebut, $dateFin]);
    }
}

