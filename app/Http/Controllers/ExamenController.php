<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $examens = Examen::with('anneeScolaire')->get();

        return $this->success('Liste des examens récupérée avec succès', $examens);
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

        return $this->success('Examen créé avec succès !', $examen, 201);
    }

    public function show(Examen $examen): JsonResponse
    {
        $examen->load('anneeScolaire');

        return $this->success('Détails de l\'examen récupérés avec succès !', $examen);
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

        return $this->success('Examen modifié avec succès !', $examen);
    }

    public function destroy(Examen $examen): JsonResponse
    {
        $examen->delete();

        return $this->success('Examen supprimé avec succès.');
    }
}