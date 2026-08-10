<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Services\AffectationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function __construct(
        protected AffectationService $affectationService
    ) {}

    public function index(): JsonResponse
    {
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

        $affectation = $this->affectationService->creer($donneesValidees);

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

        $affectation = $this->affectationService->modifier($affectation, $donneesValidees);

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