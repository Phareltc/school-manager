<?php

namespace App\Models;

use App\Models\Cours;
use App\Models\Eleve;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $table = 'presences';

    protected $fillable = [
        'eleve_id',
        'cours_id',
        'date_presence',
        'statut',
        'heure_arrivee',
        'heure_depart',
        'est_justifiee',
        'motif_absence',
    ];

    protected $casts = [
        'date_presence' => 'date',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }
}