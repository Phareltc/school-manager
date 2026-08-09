<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Services\InscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function __construct(
        protected InscriptionService $inscriptionService
    ) {}

    public function index(): JsonResponse
    {
        $inscriptions = Inscription::with(['eleve', 'classe', 'anneeScolaire'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des inscriptions récupérée avec succès',
            'data' => $inscriptions
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
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'date_inscription' => 'required|date',
            'statut' => 'in:actif,transféré,abandonné',
        ]);

        $inscription = $this->inscriptionService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Inscription créée avec succès !',
            'data' => $inscription
        ], 201);
    }

    public function show(Inscription $inscription): JsonResponse
    {
        $inscription->load(['eleve', 'classe', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'inscription récupérés avec succès !',
            'data' => $inscription
        ], 200);
    }

    public function edit(Inscription $inscription)
    {
        //
    }

    public function update(Request $request, Inscription $inscription): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'date_inscription' => 'required|date',
            'statut' => 'in:actif,transféré,abandonné',
        ]);

        $inscription = $this->inscriptionService->modifier($inscription, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Inscription modifiée avec succès !',
            'data' => $inscription
        ], 200);
    }

    public function destroy(Inscription $inscription): JsonResponse
    {
        $inscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscription supprimée avec succès.'
        ], 200);
    }
}