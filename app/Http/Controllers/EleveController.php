<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EleveController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $eleves = Eleve::all();

        return $this->success('Liste des élèves récupérée avec succès', $eleves);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => 'required|string|unique:eleves,matricule',
            'date_naissance' => 'required|date',
            'sexe' => ['required', Rule::in(['Masculin', 'Féminin'])],
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $eleve = Eleve::create($donneesValidees);

        return $this->success('Élève inscrit avec succès !', $eleve, 201);
    }

    public function show(Eleve $eleve): JsonResponse
    {
        return $this->success('Élève trouvé avec succès !', $eleve);
    }

    public function edit(Eleve $eleve)
    {
        //
    }

    public function update(Request $request, Eleve $eleve): JsonResponse
    {
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => 'required|string|unique:eleves,matricule,' . $eleve->id,
            'date_naissance' => 'required|date',
            'sexe' => ['required', Rule::in(['Masculin', 'Féminin'])],
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $eleve->update($donneesValidees);

        return $this->success('Élève modifié avec succès !', $eleve);
    }

    public function destroy(Eleve $eleve): JsonResponse
    {
        $eleve->delete();

        return $this->success('Élève supprimé avec succès.');
    }
}