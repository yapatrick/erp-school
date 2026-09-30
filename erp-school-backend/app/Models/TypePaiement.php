<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// TypePaiement Model
// ==========================================
class TypePaiement extends Model
{
    protected $table = 'types_paiement';
    protected $primaryKey = 'id_type';

    protected $fillable = [
        'code_type',
        'libelle',
        'description',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_type_paiement');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}

