<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Validation\ValidationException;

class NoteService
{
    /**
     * Crée une nouvelle note, en vérifiant l'unicité élève/examen/matière.
     */
    public function creer(array $donnees): Note
    {
        $this->verifierUnicite($donnees['eleve_id'], $donnees['examen_id'], $donnees['matiere_id']);

        return Note::create($donnees);
    }

    /**
     * Modifie une note existante, en vérifiant l'unicité (hors elle-même).
     */
    public function modifier(Note $note, array $donnees): Note
    {
        $this->verifierUnicite($donnees['eleve_id'], $donnees['examen_id'], $donnees['matiere_id'], $note->id);

        $note->update($donnees);

        return $note;
    }

    /**
     * Vérifie qu'aucune autre note n'existe déjà pour cet élève/examen/matière.
     * Lève une ValidationException si c'est le cas (Laravel la transforme automatiquement en 422 JSON).
     */
    protected function verifierUnicite(int $eleveId, int $examenId, int $matiereId, ?int $ignorerId = null): void
    {
        $dejaNote = Note::where('eleve_id', $eleveId)
            ->where('examen_id', $examenId)
            ->where('matiere_id', $matiereId)
            ->when($ignorerId, fn ($query) => $query->where('id', '!=', $ignorerId))
            ->exists();

        if ($dejaNote) {
            throw ValidationException::withMessages([
                'note' => 'Cet élève a déjà une note pour cet examen dans cette matière.',
            ]);
        }
    }
}