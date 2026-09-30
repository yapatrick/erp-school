<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// Enseignement Model
// ==========================================
class Enseignement extends Model
{
    protected $table = 'enseignements';
    protected $primaryKey = 'id_enseignement';

    protected $fillable = [
        'id_classe',
        'id_matiere',
        'id_professeur',
        'heures_semaine',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'salle'
    ];

    protected $casts = [
        'heures_semaine' => 'integer',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i'
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'id_classe');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'id_matiere');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'id_enseignement');
    }

    public function scopeParJour($query, $jour)
    {
        return $query->where('jour_semaine', $jour);
    }
}

