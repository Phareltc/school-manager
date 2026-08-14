<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $matieres = Matiere::all();

        return $this->success('Liste des matières récupérée avec succès', $matieres);
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

        return $this->success('Matière créée avec succès !', $matiere, 201);
    }

    public function show(Matiere $matiere): JsonResponse
    {
        return $this->success('Détails de la matière récupérés avec succès !', $matiere);
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

        return $this->success('Matière modifiée avec succès !', $matiere);
    }

    public function destroy(Matiere $matiere): JsonResponse
    {
        $matiere->delete();

        return $this->success('Matière supprimée avec succès.');
    }
}