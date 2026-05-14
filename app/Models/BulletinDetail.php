<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletinDetail extends Model
{
    protected $table = 'bulletin_details';

    protected $fillable = [
        'bulletin_id',
        'matiere_id',
        'moyenne_matiere',
        'appreciation_enseignant',
    ];

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    protected $casts = [
        'moyenne_matiere' => 'float:2',
    ];
}
