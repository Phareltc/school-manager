<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

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
        // ÉTAPE 1 : La sécurité (Validation)
        // On vérifie que le client envoie bien les données obligatoires et au bon format.
        // Si le nom manque ou si le téléphone n'est pas unique, Laravel s'arrête ici et renvoie une erreur.
        $donneesValidees = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => 'required|string|unique:eleves,matricule',
            'date_naissance' => 'required|date',
            'sexe' => 'required|string|max:1', // 'M' ou 'F'
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        // ÉTAPE 2 : L'insertion en base
        // Le modèle Eleve prend les données validées et crée une nouvelle ligne dans PostgreSQL.
        $eleve = Eleve::create($donneesValidees);

        // ÉTAPE 3 : La réponse
        // On renvoie un code 201 (qui signifie "Créé avec succès") et l'élève avec son tout nouvel ID.
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
            'sexe' => 'required|string|max:1', // 'M' ou 'F'
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
