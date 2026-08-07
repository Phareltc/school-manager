<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    protected $table = 'annees_scolaires';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'est_actuelle',
    ];

    // On force Laravel à traiter ce champ comme un booléen PHP,
    // même si en base c'est un tinyint (comportement PostgreSQL/MySQL).
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_actuelle' => 'boolean',
    ];
}