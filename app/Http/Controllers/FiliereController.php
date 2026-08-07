<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index(): JsonResponse
    {
        $filieres = Filiere::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des filières récupérée avec succès',
            'data' => $filieres
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255|unique:filieres,nom',
        ]);

        $filiere = Filiere::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Filière créée avec succès !',
            'data' => $filiere
        ], 201);
    }

    public function show(Filiere $filiere): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Détails de la filière récupérés avec succès !',
            'data' => $filiere
        ], 200);
    }

    public function edit(Filiere $filiere)
    {
        //
    }

    public function update(Request $request, Filiere $filiere): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255|unique:filieres,nom,' . $filiere->id,
        ]);

        $filiere->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Filière modifiée avec succès !',
            'data' => $filiere
        ], 200);
    }

    public function destroy(Filiere $filiere): JsonResponse
    {
        $filiere->delete();

        return response()->json([
            'success' => true,
            'message' => 'Filière supprimée avec succès.'
        ], 200);
    }
}