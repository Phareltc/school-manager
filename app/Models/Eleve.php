<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eleve extends Model
{
    protected $table = 'eleves';

    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'date_naissance',
        'sexe',
        'telephone',
        'adresse',
    ];

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'eleve_id');
    }
}
