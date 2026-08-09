<?php

namespace App\Services;

use App\Models\AnneeScolaire;

class AnneeScolaireService
{
    /**
     * Crée une nouvelle année scolaire.
     * Si elle est marquée comme "actuelle", désactive automatiquement les autres.
     */
    public function creer(array $donnees): AnneeScolaire
    {
        if (!empty($donnees['est_actuelle']) && $donnees['est_actuelle']) {
            AnneeScolaire::where('est_actuelle', true)->update(['est_actuelle' => false]);
        }

        return AnneeScolaire::create($donnees);
    }

    /**
     * Modifie une année scolaire existante.
     * Même règle : si elle devient "actuelle", désactive les autres (sauf elle-même).
     */
    public function modifier(AnneeScolaire $anneeScolaire, array $donnees): AnneeScolaire
    {
        if (!empty($donnees['est_actuelle']) && $donnees['est_actuelle']) {
            AnneeScolaire::where('id', '!=', $anneeScolaire->id)
                ->where('est_actuelle', true)
                ->update(['est_actuelle' => false]);
        }

        $anneeScolaire->update($donnees);

        return $anneeScolaire;
    }
}