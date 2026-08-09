<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(): JsonResponse
    {
        $presences = Presence::with(['eleve', 'cours'])->get();

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

        // Règle métier : une seule présence par élève, par cours, et par date
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

    public function show(Presence $presence): JsonResponse
    {
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

    public function destroy(Presence $presence): JsonResponse
    {
        $presence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Présence supprimée avec succès.'
        ], 200);
    }
}