<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// PeriodeEvaluation Model
// ==========================================
class PeriodeEvaluation extends Model
{
    protected $table = 'periodes_evaluation';
    protected $primaryKey = 'id_periode';

    protected $fillable = [
        'id_annee_scolaire',
        'libelle',
        'date_debut',
        'date_fin',
        'ordre',
        'active'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'ordre' => 'integer',
        'active' => 'boolean'
    ];

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'id_annee_scolaire');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'id_periode');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
