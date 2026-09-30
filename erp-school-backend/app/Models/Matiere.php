<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ==========================================
// Matiere Model
// ==========================================
class Matiere extends Model
{
    protected $table = 'matieres';
    protected $primaryKey = 'id_matiere';

    protected $fillable = [
        'code_matiere',
        'nom_matiere',
        'description',
        'coefficient',
        'active'
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'active' => 'boolean'
    ];

    public function enseignements(): HasMany
    {
        return $this->hasMany(Enseignement::class, 'id_matiere');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'id_matiere');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

