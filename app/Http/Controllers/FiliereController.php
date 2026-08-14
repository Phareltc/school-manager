<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $filieres = Filiere::all();

        return $this->success('Liste des filières récupérée avec succès', $filieres);
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

        return $this->success('Filière créée avec succès !', $filiere, 201);
    }

    public function show(Filiere $filiere): JsonResponse
    {
        return $this->success('Détails de la filière récupérés avec succès !', $filiere);
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

        return $this->success('Filière modifiée avec succès !', $filiere);
    }

    public function destroy(Filiere $filiere): JsonResponse
    {
        $filiere->delete();

        return $this->success('Filière supprimée avec succès.');
    }
}