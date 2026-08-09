<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $eleve = Eleve::all();

        return response()->json([
            'success' => true,
            'message' => 'Liste des élèves récupéré avec succès',
            'data' => $eleve
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return response()->json([
            'success' => true,
            'message' => 'Élève inscrit avec succès !',
            'data' => $eleve
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Eleve $eleve): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Elève trouvé avec succès!',
            'data' => $eleve
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eleve $eleve)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

        return response()->json([
            'success' => true,
            'message' => 'Eleve modifié avec succès!',
            'data' => $eleve
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eleve $eleve): JsonResponse
    {
        $eleve->delete();

        return response()->json([
            'success' => true,
            'message' => 'Elève supprimé avec succès!',
            'data' => $eleve
        ], 200);
    }
}