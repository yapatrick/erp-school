<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


// ==========================================
// Paiement Model
// ==========================================
class Paiement extends Model
{
    protected $table = 'paiements';
    protected $primaryKey = 'id_paiement';

    protected $fillable = [
        'id_etudiant',
        'id_inscription',
        'id_type_paiement',
        'montant',
        'mode_paiement',
        'numero_recu',
        'reference',
        'id_utilisateur_caisse',
        'remarques'
    ];

    protected $casts = [
        'montant' => 'decimal:2'
    ];

    // Génération automatique du numéro de reçu
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paiement) {
            if (empty($paiement->numero_recu)) {
                $paiement->numero_recu = 'REC-' . date('Ymd') . '-' . str_pad(
                    Paiement::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class, 'id_inscription');
    }

    public function typePaiement(): BelongsTo
    {
        return $this->belongsTo(TypePaiement::class, 'id_type_paiement');
    }

    public function caissier(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur_caisse');
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }

    public function scopeParMode($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }
}

