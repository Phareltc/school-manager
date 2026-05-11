<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enseignant extends Model
{
    protected $table = 'enseignants';

    protected $fillable = [
        'user_id',
        'specialite',
        'date_embauche',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
