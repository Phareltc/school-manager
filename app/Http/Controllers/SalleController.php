<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index(): JsonResponse
    {
        $salles = Salle::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des salles récupérée avec succès',
            'data' => $salles
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255|unique:salles,nom',
            'capacite' => 'required|integer|min:1',
        ]);

        $salle = Salle::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Salle créée avec succès !',
            'data' => $salle
        ], 201);
    }

    public function show(Salle $salle): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Détails de la salle récupérés avec succès !',
            'data' => $salle
        ], 200);
    }

    public function edit(Salle $salle)
    {
        //
    }

    public function update(Request $request, Salle $salle): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255|unique:salles,nom,' . $salle->id,
            'capacite' => 'required|integer|min:1',
        ]);

        $salle->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Salle modifiée avec succès !',
            'data' => $salle
        ], 200);
    }

    public function destroy(Salle $salle): JsonResponse
    {
        $salle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Salle supprimée avec succès.'
        ], 200);
    }
}