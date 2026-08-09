<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function index(): JsonResponse
    {
        // On charge toutes les relations pour un JSON directement exploitable côté frontend
        $affectations = Affectation::with(['enseignant.user', 'classe', 'matiere', 'anneeScolaire'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des affectations récupérée avec succès',
            'data' => $affectations
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'charge_horaire_hebdomadaire' => 'required|integer|min:1',
        ]);

        // Règle métier : un même enseignant ne peut pas être affecté deux fois
        // à la même matière, dans la même classe, pour la même année scolaire.
        $doublon = Affectation::where('enseignant_id', $donneesValidees['enseignant_id'])
            ->where('classe_id', $donneesValidees['classe_id'])
            ->where('matiere_id', $donneesValidees['matiere_id'])
            ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
            ->exists();

        if ($doublon) {
            return response()->json([
                'success' => false,
                'message' => 'Cet enseignant est déjà affecté à cette matière, dans cette classe, pour cette année scolaire.',
            ], 422);
        }

        $affectation = Affectation::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Affectation créée avec succès !',
            'data' => $affectation
        ], 201);
    }

    public function show(Affectation $affectation): JsonResponse
    {
        $affectation->load(['enseignant.user', 'classe', 'matiere', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'affectation récupérés avec succès !',
            'data' => $affectation
        ], 200);
    }

    public function edit(Affectation $affectation)
    {
        //
    }

    public function update(Request $request, Affectation $affectation): JsonResponse
    {
        $donneesValidees = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'charge_horaire_hebdomadaire' => 'required|integer|min:1',
        ]);

        // Même vérification de doublon, en excluant l'affectation qu'on est en train de modifier
        $doublon = Affectation::where('enseignant_id', $donneesValidees['enseignant_id'])
            ->where('classe_id', $donneesValidees['classe_id'])
            ->where('matiere_id', $donneesValidees['matiere_id'])
            ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
            ->where('id', '!=', $affectation->id)
            ->exists();

        if ($doublon) {
            return response()->json([
                'success' => false,
                'message' => 'Cet enseignant est déjà affecté à cette matière, dans cette classe, pour cette année scolaire.',
            ], 422);
        }

        $affectation->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Affectation modifiée avec succès !',
            'data' => $affectation
        ], 200);
    }

    public function destroy(Affectation $affectation): JsonResponse
    {
        $affectation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Affectation supprimée avec succès.'
        ], 200);
    }
}