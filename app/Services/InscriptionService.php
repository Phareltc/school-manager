<?php

namespace App\Services;

use App\Models\Classe;
use App\Models\Inscription;
use Illuminate\Validation\ValidationException;

class InscriptionService
{
    /**
     * Crée une nouvelle inscription, avec vérification d'unicité et de capacité.
     */
    public function creer(array $donnees): Inscription
    {
        $statut = $donnees['statut'] ?? 'actif';

        if ($statut === 'actif') {
            $this->verifierInscriptionActiveUnique($donnees['eleve_id'], $donnees['annee_scolaire_id']);
            $this->verifierCapaciteClasse($donnees['classe_id']);
        }

        return Inscription::create($donnees);
    }

    /**
     * Modifie une inscription existante, avec les mêmes vérifications (en s'excluant elle-même).
     */
    public function modifier(Inscription $inscription, array $donnees): Inscription
    {
        $statut = $donnees['statut'] ?? 'actif';

        if ($statut === 'actif') {
            $this->verifierInscriptionActiveUnique($donnees['eleve_id'], $donnees['annee_scolaire_id'], $inscription->id);
            $this->verifierCapaciteClasse($donnees['classe_id'], $inscription->id);
        }

        $inscription->update($donnees);

        return $inscription;
    }

    /**
     * Vérifie qu'un élève n'a pas déjà une inscription active pour cette année scolaire.
     */
    protected function verifierInscriptionActiveUnique(int $eleveId, int $anneeScolaireId, ?int $ignorerId = null): void
    {
        $dejaInscritActif = Inscription::where('eleve_id', $eleveId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->where('statut', 'actif')
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->exists();

        if ($dejaInscritActif) {
            throw ValidationException::withMessages([
                'eleve_id' => 'Cet élève possède déjà une inscription active pour cette année scolaire.',
            ]);
        }
    }

    /**
     * Vérifie que la classe n'a pas atteint sa capacité maximale d'élèves inscrits actifs.
     */
    protected function verifierCapaciteClasse(int $classeId, ?int $ignorerId = null): void
    {
        $classe = Classe::findOrFail($classeId);

        $nombreInscritsActifs = Inscription::where('classe_id', $classeId)
            ->where('statut', 'actif')
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->count();

        if ($nombreInscritsActifs >= $classe->capacite_max) {
            throw ValidationException::withMessages([
                'classe_id' => "Cette classe a atteint sa capacité maximale ({$classe->capacite_max} élèves).",
            ]);
        }
    }
}