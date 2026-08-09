<?php

namespace App\Services;

use App\Models\Affectation;
use App\Models\Cours;
use App\Models\Inscription;
use App\Models\Salle;
use Illuminate\Validation\ValidationException;

class CoursService
{
    /**
     * Crée un nouveau cours, avec toutes les vérifications métier.
     */
    public function creer(array $donnees): Cours
    {
        $affectation = Affectation::findOrFail($donnees['affectation_id']);

        $this->verifierChevauchementHoraire($affectation->enseignant_id, $donnees['jour_semaine'], $donnees['heure_debut'], $donnees['heure_fin']);
        $this->verifierCapaciteSalle($donnees['salle_id'], $affectation->classe_id);

        return Cours::create($donnees);
    }

    /**
     * Modifie un cours existant, avec les mêmes vérifications (en s'excluant lui-même).
     */
    public function modifier(Cours $cours, array $donnees): Cours
    {
        $affectation = Affectation::findOrFail($donnees['affectation_id']);

        $this->verifierChevauchementHoraire($affectation->enseignant_id, $donnees['jour_semaine'], $donnees['heure_debut'], $donnees['heure_fin'], $cours->id);
        $this->verifierCapaciteSalle($donnees['salle_id'], $affectation->classe_id);

        $cours->update($donnees);

        return $cours;
    }

    /**
     * Vérifie qu'un enseignant n'a pas déjà un cours qui chevauche cet horaire, ce jour-là.
     */
    protected function verifierChevauchementHoraire(int $enseignantId, string $jourSemaine, string $heureDebut, string $heureFin, ?int $ignorerId = null): void
    {
        $conflit = Cours::whereHas('affectation', function ($query) use ($enseignantId) {
                $query->where('enseignant_id', $enseignantId);
            })
            ->where('jour_semaine', $jourSemaine)
            ->where('heure_debut', '<', $heureFin)
            ->where('heure_fin', '>', $heureDebut)
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->exists();

        if ($conflit) {
            throw ValidationException::withMessages([
                'heure_debut' => 'Cet enseignant a déjà un cours sur ce créneau horaire.',
            ]);
        }
    }

    /**
     * Vérifie que la salle choisie peut accueillir tous les élèves actifs de la classe concernée.
     */
    protected function verifierCapaciteSalle(int $salleId, int $classeId): void
    {
        $salle = Salle::findOrFail($salleId);

        $nombreElevesClasse = Inscription::where('classe_id', $classeId)
            ->where('statut', 'actif')
            ->count();

        if ($nombreElevesClasse > $salle->capacite) {
            throw ValidationException::withMessages([
                'salle_id' => "La salle choisie (capacité {$salle->capacite}) est trop petite pour cette classe ({$nombreElevesClasse} élèves inscrits).",
            ]);
        }
    }
}