<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affectation extends Model
{
    // Toujours forcer la table
    protected $table = 'affectations';

    protected $fillable = [
        'enseignant_id',
        'classe_id',
        'matiere_id',
        'annee_scolaire_id',
        'charge_horaire_hebdomadaire',
    ];

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function classe(): BelongsTo 
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);   
    }

    public  function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    } 
}
