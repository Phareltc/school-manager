<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnseignantController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $enseignants = Enseignant::with('user')->get();

        return $this->success('Liste des enseignants récupérée avec succès', $enseignants);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'user_id' => 'required|exists:users,id|unique:enseignants,user_id',
            'specialite' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        $enseignant = Enseignant::create($donneesValidees);

        return $this->success('Enseignant créé avec succès !', $enseignant, 201);
    }

    public function show(Enseignant $enseignant): JsonResponse
    {
        $enseignant->load('user');

        return $this->success('Détails de l\'enseignant récupérés avec succès !', $enseignant);
    }

    public function edit(Enseignant $enseignant)
    {
        //
    }

    public function update(Request $request, Enseignant $enseignant): JsonResponse
    {
        $donneesValidees = $request->validate([
            'user_id' => 'required|exists:users,id|unique:enseignants,user_id,' . $enseignant->id,
            'specialite' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        $enseignant->update($donneesValidees);

        return $this->success('Enseignant modifié avec succès !', $enseignant);
    }

    public function destroy(Enseignant $enseignant): JsonResponse
    {
        $enseignant->delete();

        return $this->success('Enseignant supprimé avec succès.');
    }
}