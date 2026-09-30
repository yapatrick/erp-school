<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// ==========================================
// Professeur Model
// ==========================================
class Professeur extends Model
{
    protected $table = 'professeurs';
    protected $primaryKey = 'id_professeur';

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
        'specialite',
        'date_embauche',
        'salaire',
        'actif'
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'salaire' => 'decimal:2',
        'actif' => 'boolean'
    ];

    protected $appends = ['nom_complet'];

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'id_professeur');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'id_professeur');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'id_professeur');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}

