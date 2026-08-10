<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Services\BulletinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function __construct(
        protected BulletinService $bulletinService
    ) {}

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

        $bulletin = $this->bulletinService->creer($donneesValidees);

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

        $bulletin = $this->bulletinService->modifier($bulletin, $donneesValidees);

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