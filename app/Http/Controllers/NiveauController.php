<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // 1. On demande au modèle d'aller chercher tous les niveaux en base PostgreSQL
        $niveaux = Niveau::all();

        // 2. Le serveur répond au client avec un statut 200 (OK) et les données au format JSON
        return response()->json([
            'success' => true,
            'message' => 'Liste des niveaux récupérée avec succès',
            'data' => $niveaux
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:niveaux,libelle',
        ]);

        $niveau = Niveau::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Niveau créé avec succès !',
            'data' => $niveau
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Niveau $niveau): JsonResponse
    {
        // Pas de recherche à faire, $niveau contient déjà toutes les données de la base !
        return response()->json([
            'success' => true,
            'message' => 'Détails du niveau récupérés avec succès !',
            'data' => $niveau
        ], 200);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Niveau $niveau)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Niveau $niveau): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:niveaux,libelle,' . $niveau->id,
        ]);

        // ici dans la ligne qui suit on écrase la précédente valeur récupérée
        $niveau->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Niveau modifié avec succès!',
            'data' => $niveau
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Niveau $niveau): JsonResponse
    {
        // L'objet sait déjà qui il est, il exécute la suppression directement
        $niveau->delete();

        return response()->json([
            'success' => true,
            'message' => 'Niveau supprimé avec succès.'
        ], 200);
    }
}
