<?php

namespace App\Services;

use App\Models\Affectation;
use Illuminate\Validation\ValidationException;

class AffectationService
{
    /**
     * Crée une nouvelle affectation, en vérifiant l'absence de doublon.
     */
    public function creer(array $donnees): Affectation
    {
        $this->verifierUnicite(
            $donnees['enseignant_id'],
            $donnees['classe_id'],
            $donnees['matiere_id'],
            $donnees['annee_scolaire_id']
        );

        return Affectation::create($donnees);
    }

    /**
     * Modifie une affectation existante, avec la même vérification (hors elle-même).
     */
    public function modifier(Affectation $affectation, array $donnees): Affectation
    {
        $this->verifierUnicite(
            $donnees['enseignant_id'],
            $donnees['classe_id'],
            $donnees['matiere_id'],
            $donnees['annee_scolaire_id'],
            $affectation->id
        );

        $affectation->update($donnees);

        return $affectation;
    }

    /**
     * Vérifie qu'un même enseignant n'est pas déjà affecté à cette matière,
     * dans cette classe, pour cette année scolaire.
     */
    protected function verifierUnicite(int $enseignantId, int $classeId, int $matiereId, int $anneeScolaireId, ?int $ignorerId = null): void
    {
        $doublon = Affectation::where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->exists();

        if ($doublon) {
            throw ValidationException::withMessages([
                'enseignant_id' => 'Cet enseignant est déjà affecté à cette matière, dans cette classe, pour cette année scolaire.',
            ]);
        }
    }
}