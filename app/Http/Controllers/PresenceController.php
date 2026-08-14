<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Cours;
use App\Models\Enseignant;
use App\Models\Presence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $presences = Presence::with(['eleve', 'cours'])->get();
        } else {
            $coursIds = $this->coursDeLEnseignant($user);

            $presences = $coursIds->isEmpty()
                ? collect()
                : Presence::with(['eleve', 'cours'])->whereIn('cours_id', $coursIds)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des présences récupérée avec succès',
            'data' => $presences
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'cours_id' => 'required|exists:cours,id',
            'date_presence' => 'required|date',
            'statut' => 'required|string|in:present,absent,retard',
            'heure_arrivee' => 'nullable|date_format:H:i',
            'heure_depart' => 'nullable|date_format:H:i',
            'est_justifiee' => 'boolean',
            'motif_absence' => 'nullable|string',
        ]);

        if (!$this->peutAgirSurCeCours($request->user(), $donneesValidees['cours_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à enregistrer une présence sur ce cours.',
            ], 403);
        }

        $dejaPresence = Presence::where('eleve_id', $donneesValidees['eleve_id'])
            ->where('cours_id', $donneesValidees['cours_id'])
            ->where('date_presence', $donneesValidees['date_presence'])
            ->exists();

        if ($dejaPresence) {
            return response()->json([
                'success' => false,
                'message' => 'Une présence a déjà été enregistrée pour cet élève, ce cours, à cette date.',
            ], 422);
        }

        $presence = Presence::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Présence enregistrée avec succès !',
            'data' => $presence
        ], 201);
    }

    public function show(Request $request, Presence $presence): JsonResponse
    {
        if (!$this->peutAgirSurCeCours($request->user(), $presence->cours_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à consulter cette présence.',
            ], 403);
        }

        $presence->load(['eleve', 'cours']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de la présence récupérés avec succès !',
            'data' => $presence
        ], 200);
    }

    public function edit(Presence $presence)
    {
        //
    }

    public function update(Request $request, Presence $presence): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'cours_id' => 'required|exists:cours,id',
            'date_presence' => 'required|date',
            'statut' => 'required|string|in:present,absent,retard',
            'heure_arrivee' => 'nullable|date_format:H:i',
            'heure_depart' => 'nullable|date_format:H:i',
            'est_justifiee' => 'boolean',
            'motif_absence' => 'nullable|string',
        ]);

        if (!$this->peutAgirSurCeCours($request->user(), $donneesValidees['cours_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cette présence.',
            ], 403);
        }

        $dejaPresence = Presence::where('eleve_id', $donneesValidees['eleve_id'])
            ->where('cours_id', $donneesValidees['cours_id'])
            ->where('date_presence', $donneesValidees['date_presence'])
            ->where('id', '!=', $presence->id)
            ->exists();

        if ($dejaPresence) {
            return response()->json([
                'success' => false,
                'message' => 'Une présence a déjà été enregistrée pour cet élève, ce cours, à cette date.',
            ], 422);
        }

        $presence->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Présence modifiée avec succès !',
            'data' => $presence
        ], 200);
    }

    public function destroy(Request $request, Presence $presence): JsonResponse
    {
        if (!$this->peutAgirSurCeCours($request->user(), $presence->cours_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer cette présence.',
            ], 403);
        }

        $presence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Présence supprimée avec succès.'
        ], 200);
    }

    /**
     * Retourne les IDs de cours dont l'enseignant connecté est responsable (via ses affectations).
     */
    protected function coursDeLEnseignant($user)
    {
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        if (!$enseignant) {
            return collect();
        }

        $affectationIds = Affectation::where('enseignant_id', $enseignant->id)->pluck('id');

        return Cours::whereIn('affectation_id', $affectationIds)->pluck('id');
    }

    /**
     * Vérifie que l'utilisateur (admin, ou enseignant responsable de ce cours précis) peut agir dessus.
     * IMPORTANT : on ne fait jamais confiance à un cours_id envoyé par le client sans vérifier
     * qu'il appartient réellement à l'enseignant connecté.
     */
    protected function peutAgirSurCeCours($user, int $coursId): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $this->coursDeLEnseignant($user)->contains($coursId);
    }
}