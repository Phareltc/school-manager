<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cours extends Model
{
    protected $table = 'cours';

    protected $fillable = [
        'affectation_id',
        'salle_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
    ];

    public function affectation(): BelongsTo 
    {
        return $this->belongsTo(Affectation::class);
    }

    public function salle(): BelongsTo 
    {
        return $this->belongsTo(Salle::class);
    }
}
