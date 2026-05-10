<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $table = 'niveaux'; // Forçage du pluriel français

    protected $fillable = ['libelle'];
}
