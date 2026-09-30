<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// Inscription Model
// ==========================================
class Inscription extends Model
{
    protected $table = 'inscriptions';
    protected $primaryKey = 'id_inscription';

    protected $fillable = [
        'id_etudiant',
        'id_classe',
        'id_annee_scolaire',
        'type_inscription',
        'statut',
        'montant_total',
        'montant_paye',
        'remarques'
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'solde' => 'decimal:2'
    ];

    protected $appends = ['est_solde'];

    public function getEstSoldeAttribute(): bool
    {
        return $this->solde <= 0;
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'id_classe');
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'id_annee_scolaire');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_inscription');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'En cours');
    }

    public function scopeValidee($query)
    {
        return $query->where('statut', 'Validée');
    }

    public function scopeAvecSolde($query)
    {
        return $query->whereRaw('montant_total - montant_paye > 0');
    }
}

