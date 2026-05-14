<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bulletin extends Model
{
    protected $table = 'bulletins';

    protected $fillable = [
        'eleve_id',
        'annee_scolaire_id',
        'moyenne_generale',
        'rang',
        'appreciation',
    ];

    public function eleve(): BelongsTo{
        return $this->belongsTo(Eleve::class);
    }

    public function anneeScolaire(): BelongsTo{
        return $this->belongsTo(AnneeScolaire::class);
    }

    protected $casts = [
        'moyenne_generale' => 'decimal:2',
    ];
}
