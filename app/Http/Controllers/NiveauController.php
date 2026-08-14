<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $niveaux = Niveau::all();

        return $this->success('Liste des niveaux récupérée avec succès', $niveaux);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:niveaux,libelle',
        ]);

        $niveau = Niveau::create($donneesValidees);

        return $this->success('Niveau créé avec succès !', $niveau, 201);
    }

    public function show(Niveau $niveau): JsonResponse
    {
        return $this->success('Détails du niveau récupérés avec succès !', $niveau);
    }

    public function edit(Niveau $niveau)
    {
        //
    }

    public function update(Request $request, Niveau $niveau): JsonResponse
    {
        $donneesValidees = $request->validate([
            'libelle' => 'required|string|max:255|unique:niveaux,libelle,' . $niveau->id,
        ]);

        $niveau->update($donneesValidees);

        return $this->success('Niveau modifié avec succès !', $niveau);
    }

    public function destroy(Niveau $niveau): JsonResponse
    {
        $niveau->delete();

        return $this->success('Niveau supprimé avec succès.');
    }
}