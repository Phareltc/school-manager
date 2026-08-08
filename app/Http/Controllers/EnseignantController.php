<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnseignantController extends Controller
{
    public function index(): JsonResponse
    {
        // On charge la relation user pour avoir le nom/email de l'enseignant directement dans le JSON
        $enseignants = Enseignant::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des enseignants récupérée avec succès',
            'data' => $enseignants
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            // unique:enseignants,user_id empêche qu'un même compte users soit lié à deux fiches enseignant
            'user_id' => 'required|exists:users,id|unique:enseignants,user_id',
            'specialite' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        $enseignant = Enseignant::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Enseignant créé avec succès !',
            'data' => $enseignant
        ], 201);
    }

    public function show(Enseignant $enseignant): JsonResponse
    {
        $enseignant->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'enseignant récupérés avec succès !',
            'data' => $enseignant
        ], 200);
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

        return response()->json([
            'success' => true,
            'message' => 'Enseignant modifié avec succès !',
            'data' => $enseignant
        ], 200);
    }

    public function destroy(Enseignant $enseignant): JsonResponse
    {
        $enseignant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enseignant supprimé avec succès.'
        ], 200);
    }
}