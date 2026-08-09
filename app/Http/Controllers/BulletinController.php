<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function index(): JsonResponse
    {
        $bulletins = Bulletin::with(['eleve', 'anneeScolaire'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des bulletins récupérée avec succès',
            'data' => $bulletins
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
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'rang' => 'required|integer|min:1',
            'appreciation' => 'required|string',
        ]);

        // Règle métier : un élève ne peut avoir qu'un seul bulletin par année scolaire
        $dejaBulletin = Bulletin::where('eleve_id', $donneesValidees['eleve_id'])
            ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
            ->exists();

        if ($dejaBulletin) {
            return response()->json([
                'success' => false,
                'message' => 'Cet élève possède déjà un bulletin pour cette année scolaire.',
            ], 422);
        }

        $bulletin = Bulletin::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Bulletin créé avec succès !',
            'data' => $bulletin
        ], 201);
    }

    public function show(Bulletin $bulletin): JsonResponse
    {
        $bulletin->load(['eleve', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails du bulletin récupérés avec succès !',
            'data' => $bulletin
        ], 200);
    }

    public function edit(Bulletin $bulletin)
    {
        //
    }

    public function update(Request $request, Bulletin $bulletin): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'rang' => 'required|integer|min:1',
            'appreciation' => 'required|string',
        ]);

        $dejaBulletin = Bulletin::where('eleve_id', $donneesValidees['eleve_id'])
            ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
            ->where('id', '!=', $bulletin->id)
            ->exists();

        if ($dejaBulletin) {
            return response()->json([
                'success' => false,
                'message' => 'Cet élève possède déjà un bulletin pour cette année scolaire.',
            ], 422);
        }

        $bulletin->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Bulletin modifié avec succès !',
            'data' => $bulletin
        ], 200);
    }

    public function destroy(Bulletin $bulletin): JsonResponse
    {
        $bulletin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bulletin supprimé avec succès.'
        ], 200);
    }
}