<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_scolaire_id',
        'date_inscription',
        'statut',
    ];

    protected $casts = [
        'date_inscription' => 'date',
    ];

    /**
     * Une inscription appartient à un élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Une inscription appartient à une classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Une inscription appartient à une année scolaire
     */
    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}
