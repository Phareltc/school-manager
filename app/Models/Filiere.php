<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $table = 'filieres';

    protected $fillable = [
        'nom',
    ];

    /**
     * Relation : Une filière peut avoir plusieurs classes
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'filiere_id');
    }
}