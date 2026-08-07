<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function index(): JsonResponse
    {
        $classes = Classe::with(['niveau', 'filiere'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des classes récupérée avec succès',
            'data' => $classes
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $classe = Classe::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Classe créée avec succès !',
            'data' => $classe
        ], 201);
    }

    public function show(Classe $classe): JsonResponse
    {
        $classe->load(['niveau', 'filiere']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de la classe récupérés avec succès !',
            'data' => $classe
        ], 200);
    }

    public function edit(Classe $classe)
    {
        //
    }

    public function update(Request $request, Classe $classe): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $classe->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Classe modifiée avec succès !',
            'data' => $classe
        ], 200);
    }

    public function destroy(Classe $classe): JsonResponse
    {
        $classe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Classe supprimée avec succès.'
        ], 200);
    }
}