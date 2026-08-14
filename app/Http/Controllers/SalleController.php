<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $salles = Salle::all();

        return $this->success('Liste des salles récupérée avec succès', $salles);
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

        return $this->success('Salle créée avec succès !', $salle, 201);
    }

    public function show(Salle $salle): JsonResponse
    {
        return $this->success('Détails de la salle récupérés avec succès !', $salle);
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

        return $this->success('Salle modifiée avec succès !', $salle);
    }

    public function destroy(Salle $salle): JsonResponse
    {
        $salle->delete();

        return $this->success('Salle supprimée avec succès.');
    }
}