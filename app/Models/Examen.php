<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examen extends Model
{
    protected $table = 'examens';

    protected $fillable = [
        'libelle',
        'type',
        'annee_scolaire_id',
    ];

    public  function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    } 
}
