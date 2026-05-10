<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    // Règle critique : Définition explicite de la table
    protected $table = 'annees_scolaires';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'est_actuelle',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_actuelle' => 'boolean',
    ];
}
