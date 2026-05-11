<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    protected $table = 'eleves';

    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'date_naissance',
        'sexe',
        'telephone',
        'adresse',
    ];
}
