<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function index(): JsonResponse
    {
        $examens = Examen::with('anneeScolaire')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des examens récupérée avec succès',
            'data' => $examens
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
        ]);

        $examen = Examen::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Examen créé avec succès !',
            'data' => $examen
        ], 201);
    }

    public function show(Examen $examen): JsonResponse
    {
        $examen->load('anneeScolaire');

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'examen récupérés avec succès !',
            'data' => $examen
        ], 200);
    }

    public function edit(Examen $examen)
    {
        //
    }

    public function update(Request $request, Examen $examen): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
        ]);

        $examen->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Examen modifié avec succès !',
            'data' => $examen
        ], 200);
    }

    public function destroy(Examen $examen): JsonResponse
    {
        $examen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Examen supprimé avec succès.'
        ], 200);
    }
}