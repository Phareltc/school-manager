<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres'; // Forçage du pluriel français

    protected $fillable = [
        'libelle',
        'code_matiere',
    ];
}
