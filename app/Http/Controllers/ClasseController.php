<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $classes = Classe::with(['niveau', 'filiere'])->get();

        return $this->success('Liste des classes récupérée avec succès', $classes);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $classe = Classe::create($donneesValidees);

        return $this->success('Classe créée avec succès !', $classe, 201);
    }

    public function show(Classe $classe): JsonResponse
    {
        $classe->load(['niveau', 'filiere']);

        return $this->success('Détails de la classe récupérés avec succès !', $classe);
    }

    public function edit(Classe $classe)
    {
        //
    }

    public function update(Request $request, Classe $classe): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $classe->update($donneesValidees);

        return $this->success('Classe modifiée avec succès !', $classe);
    }

    public function destroy(Classe $classe): JsonResponse
    {
        $classe->delete();

        return $this->success('Classe supprimée avec succès.');
    }
}