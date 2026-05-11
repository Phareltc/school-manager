<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Champs autorisés en insertion massive
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'est_actif',
    ];

    /**
     * Champs cachés dans les réponses JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversion automatique des types
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'est_actif' => 'boolean',
        ];
    }
}