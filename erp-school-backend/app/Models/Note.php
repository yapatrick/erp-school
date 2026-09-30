<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// Note Model
// ==========================================
class Note extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id_note';

    protected $fillable = [
        'id_etudiant',
        'id_matiere',
        'id_professeur',
        'id_periode',
        'type_evaluation',
        'note',
        'note_sur',
        'date_evaluation',
        'observations'
    ];

    protected $casts = [
        'note' => 'decimal:2',
        'note_sur' => 'decimal:2',
        'date_evaluation' => 'date'
    ];

    protected $appends = ['note_sur_vingt', 'pourcentage'];

    public function getNoteSurVingtAttribute(): float
    {
        return ($this->note / $this->note_sur) * 20;
    }

    public function getPourcentageAttribute(): float
    {
        return ($this->note / $this->note_sur) * 100;
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'id_matiere');
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'id_professeur');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeEvaluation::class, 'id_periode');
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_evaluation', $type);
    }
}

