<?php

namespace App\Services;

use App\Models\Bulletin;
use Illuminate\Validation\ValidationException;

class BulletinService
{
    /**
     * Crée un nouveau bulletin, en vérifiant qu'un élève n'a qu'un seul bulletin par année scolaire.
     */
    public function creer(array $donnees): Bulletin
    {
        $this->verifierUnicite($donnees['eleve_id'], $donnees['annee_scolaire_id']);

        return Bulletin::create($donnees);
    }

    /**
     * Modifie un bulletin existant, avec la même vérification (hors lui-même).
     */
    public function modifier(Bulletin $bulletin, array $donnees): Bulletin
    {
        $this->verifierUnicite($donnees['eleve_id'], $donnees['annee_scolaire_id'], $bulletin->id);

        $bulletin->update($donnees);

        return $bulletin;
    }

    /**
     * Vérifie qu'aucun autre bulletin n'existe déjà pour cet élève, cette année scolaire.
     */
    protected function verifierUnicite(int $eleveId, int $anneeScolaireId, ?int $ignorerId = null): void
    {
        $dejaBulletin = Bulletin::where('eleve_id', $eleveId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->exists();

        if ($dejaBulletin) {
            throw ValidationException::withMessages([
                'eleve_id' => 'Cet élève possède déjà un bulletin pour cette année scolaire.',
            ]);
        }
    }
}