<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Services\AnneeScolaireService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AnneeScolaireService $anneeScolaireService
    ) {}

    public function index(): JsonResponse
    {
        $anneesScolaires = AnneeScolaire::all();

        return $this->success('Liste des années scolaires récupérée avec succès', $anneesScolaires);
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

        $anneeScolaire = $this->anneeScolaireService->creer($donneesValidees);

        return $this->success('Année scolaire créée avec succès !', $anneeScolaire, 201);
    }

    public function show(AnneeScolaire $anneeScolaire): JsonResponse
    {
        return $this->success('Détails de l\'année scolaire récupérés avec succès !', $anneeScolaire);
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

        return $this->success('Année scolaire modifiée avec succès !', $anneeScolaire);
    }

    public function destroy(AnneeScolaire $anneeScolaire): JsonResponse
    {
        $anneeScolaire->delete();

        return $this->success('Année scolaire supprimée avec succès.');
    }
}