<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    // 1. Définition explicite de la table (Règle métier)
    protected $table = 'classes';

    // 2. Autorisation des champs pour l'insertion de masse
    protected $fillable = [
        'nom',
        'capacite_max',
        'niveau_id', // Très important : la clé étrangère doit être fillable !
    ];

    /**
     * Relation : Une classe appartient à un niveau (N-1)
     */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }
}
