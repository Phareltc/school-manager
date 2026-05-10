<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $table = 'salles';

    protected $fillable = [
        'nom',
        'capacite',
    ];
}
