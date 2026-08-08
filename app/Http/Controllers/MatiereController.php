<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index(): JsonResponse
    {
        $matieres = Matiere::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des matières récupérée avec succès',
            'data' => $matieres
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
            'code_matiere' => 'required|string|max:50|unique:matieres,code_matiere',
        ]);

        $matiere = Matiere::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Matière créée avec succès !',
            'data' => $matiere
        ], 201);
    }

    public function show(Matiere $matiere): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Détails de la matière récupérés avec succès !',
            'data' => $matiere
        ], 200);
    }

    public function edit(Matiere $matiere)
    {
        //
    }

    public function update(Request $request, Matiere $matiere): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255',
            'code_matiere' => 'required|string|max:50|unique:matieres,code_matiere,' . $matiere->id,
        ]);

        $matiere->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Matière modifiée avec succès !',
            'data' => $matiere
        ], 200);
    }

    public function destroy(Matiere $matiere): JsonResponse
    {
        $matiere->delete();

        return response()->json([
            'success' => true,
            'message' => 'Matière supprimée avec succès.'
        ], 200);
    }
}