<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Enseignant;
use App\Models\Examen;
use App\Models\Inscription;
use App\Models\Note;
use App\Services\NoteService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected NoteService $noteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $notes = Note::with(['eleve', 'examen', 'matiere', 'enseignant.user'])->get();
        } else {
            $affectations = $this->affectationsDeLUtilisateur($user);

            $notes = $affectations->isEmpty()
                ? collect()
                : Note::with(['eleve', 'examen', 'matiere', 'enseignant.user'])
                    ->where(function ($query) use ($affectations) {
                        foreach ($affectations as $affectation) {
                            $query->orWhere(function ($q) use ($affectation) {
                                $q->where('matiere_id', $affectation->matiere_id)
                                  ->whereHas('examen', fn ($eq) => $eq->where('annee_scolaire_id', $affectation->annee_scolaire_id))
                                  ->whereHas('eleve.inscriptions', fn ($iq) => $iq
                                      ->where('classe_id', $affectation->classe_id)
                                      ->where('annee_scolaire_id', $affectation->annee_scolaire_id)
                                      ->where('statut', 'actif'));
                            });
                        }
                    })
                    ->get();
        }

        return $this->success('Liste des notes récupérée avec succès', $notes);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'examen_id' => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        if (!$this->estAutorisePourCetteCombinaison($request->user(), $donneesValidees['matiere_id'], $donneesValidees['eleve_id'], $donneesValidees['examen_id'])) {
            return $this->error('Vous n\'êtes pas affecté à cette matière pour la classe de cet élève.', 403);
        }

        $note = $this->noteService->creer($donneesValidees);

        return $this->success('Note créée avec succès !', $note, 201);
    }

    public function show(Request $request, Note $note): JsonResponse
    {
        if (!$this->estAutorisePourCetteCombinaison($request->user(), $note->matiere_id, $note->eleve_id, $note->examen_id)) {
            return $this->error('Vous n\'êtes pas autorisé à consulter cette note.', 403);
        }

        $note->load(['eleve', 'examen', 'matiere', 'enseignant.user']);

        return $this->success('Détails de la note récupérés avec succès !', $note);
    }

    public function edit(Note $note)
    {
        //
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'examen_id' => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        if (!$this->estAutorisePourCetteCombinaison($request->user(), $donneesValidees['matiere_id'], $donneesValidees['eleve_id'], $donneesValidees['examen_id'])) {
            return $this->error('Vous n\'êtes pas affecté à cette matière pour la classe de cet élève.', 403);
        }

        $note = $this->noteService->modifier($note, $donneesValidees);

        return $this->success('Note modifiée avec succès !', $note);
    }

    public function destroy(Request $request, Note $note): JsonResponse
    {
        if (!$this->estAutorisePourCetteCombinaison($request->user(), $note->matiere_id, $note->eleve_id, $note->examen_id)) {
            return $this->error('Vous n\'êtes pas autorisé à supprimer cette note.', 403);
        }

        $note->delete();

        return $this->success('Note supprimée avec succès.');
    }

    protected function affectationsDeLUtilisateur($user)
    {
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        return $enseignant
            ? Affectation::where('enseignant_id', $enseignant->id)->get()
            : collect();
    }

    protected function estAutorisePourCetteCombinaison($user, int $matiereId, int $eleveId, int $examenId): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $enseignant = Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return false;
        }

        $anneeScolaireId = Examen::find($examenId)?->annee_scolaire_id;
        if (!$anneeScolaireId) {
            return false;
        }

        $classeId = Inscription::where('eleve_id', $eleveId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->where('statut', 'actif')
            ->value('classe_id');

        if (!$classeId) {
            return false;
        }

        return Affectation::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $matiereId)
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->exists();
    }
}