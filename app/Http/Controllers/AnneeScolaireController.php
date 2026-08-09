<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Services\AnneeScolaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    // ICI : on déclare qu'on a besoin d'un AnneeScolaireService.
    // Laravel (le "patron") va automatiquement le fournir, sans qu'on écrive new AnneeScolaireService().
    public function __construct(
        protected AnneeScolaireService $anneeScolaireService
    ) {}

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

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:annees_scolaires,libelle',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'est_actuelle' => 'boolean',
        ]);

        // AVANT : toute la logique métier était ici, dans le contrôleur.
        // MAINTENANT : le contrôleur délègue au spécialiste (le Service).
        $anneeScolaire = $this->anneeScolaireService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire créée avec succès !',
            'data' => $anneeScolaire
        ], 201);
    }

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

    public function update(Request $request, AnneeScolaire $anneeScolaire): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:annees_scolaires,libelle,' . $anneeScolaire->id,
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'est_actuelle' => 'boolean',
        ]);

        $anneeScolaire = $this->anneeScolaireService->modifier($anneeScolaire, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire modifiée avec succès !',
            'data' => $anneeScolaire
        ], 200);
    }

    public function destroy(AnneeScolaire $anneeScolaire): JsonResponse
    {
        $anneeScolaire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Année scolaire supprimée avec succès.'
        ], 200);
    }
}