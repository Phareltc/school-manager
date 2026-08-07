<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $anneesScolaires = AnneeScolaire::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des années scolaires récupérée avec succès',
            'data' => $anneesScolaires
        ], 200);
    }

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
            'libelle' => 'required|string|max:255|unique:annees_scolaires,libelle',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'est_actuelle' => 'boolean',
        ]);

        // Règle métier : une seule année active à la fois.
        // Si la nouvelle année est marquée comme actuelle, on désactive toutes les autres AVANT de créer.
        if (!empty($donneesValidees['est_actuelle']) && $donneesValidees['est_actuelle']) {
            AnneeScolaire::where('est_actuelle', true)->update(['est_actuelle' => false]);
        }

        $anneeScolaire = AnneeScolaire::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire créée avec succès !',
            'data' => $anneeScolaire
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AnneeScolaire $anneeScolaire): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'année scolaire récupérés avec succès !',
            'data' => $anneeScolaire
        ], 200);
    }

    public function edit(AnneeScolaire $anneeScolaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnneeScolaire $anneeScolaire): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:annees_scolaires,libelle,' . $anneeScolaire->id,
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'est_actuelle' => 'boolean',
        ]);

        // Même règle métier ici : si on active celle-ci, on désactive les autres
        // (sauf elle-même, sinon on se désactiverait avant de se mettre à jour).
        if (!empty($donneesValidees['est_actuelle']) && $donneesValidees['est_actuelle']) {
            AnneeScolaire::where('id', '!=', $anneeScolaire->id)
                ->where('est_actuelle', true)
                ->update(['est_actuelle' => false]);
        }

        $anneeScolaire->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire modifiée avec succès !',
            'data' => $anneeScolaire
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnneeScolaire $anneeScolaire): JsonResponse
    {
        $anneeScolaire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire supprimée avec succès.'
        ], 200);
    }
}
