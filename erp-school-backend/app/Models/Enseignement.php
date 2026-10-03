<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- AJOUT IMPORTANT

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
        return $this->belongsTo(Classe::class, 'id_classe', 'id_classe');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'id_matiere', 'id_matiere');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur', 'id_professeur');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'id_enseignement', 'id_enseignement');
    }

    // Scope pour filtrer les enseignements par jour de la semaine
     public function scopeParJour($query, $jour)
    {
        return $query->where('jour_semaine', $jour);
    }

    // Scope pour filtrer les enseignements par classe  
    
    public function scopeParClasse($query, $idClasse)
    {
        return $query->where('id_classe', $idClasse);
    }

    // Scope pour filtrer les enseignements par professeur  

    public function scopeParProfesseur($query, $idProfesseur)
    {
        return $query->where('id_professeur', $idProfesseur);
    }

    // Scope pour filtrer les enseignements par matière
    public function scopeParMatiere($query, $idMatiere)
    {
        return $query->where('id_matiere', $idMatiere);
    }

    // Scope pour filtrer les enseignements par heure de début
    public function scopeParHeureDebut($query, $heureDebut)
    {
        return $query->where('heure_debut', $heureDebut);
    }
    
    // Scope pour filtrer les enseignements par heure de fin
    public function scopeParHeureFin($query, $heureFin)
    {
        return $query->where('heure_fin', $heureFin);
    }

}

