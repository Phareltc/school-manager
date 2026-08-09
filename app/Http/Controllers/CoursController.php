<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    public function index(): JsonResponse
    {
        $cours = Cours::with(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des cours récupérée avec succès',
            'data' => $cours
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => 'required|string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        // Règle métier : empêcher un enseignant d'avoir deux cours au même horaire.
        // On récupère l'enseignant lié à l'affectation demandée, puis on cherche
        // si un cours existant, pour ce même enseignant, ce même jour, chevauche l'horaire demandé.
        $enseignantId = \App\Models\Affectation::findOrFail($donneesValidees['affectation_id'])->enseignant_id;

        $conflit = Cours::whereHas('affectation', function ($query) use ($enseignantId) {
                $query->where('enseignant_id', $enseignantId);
            })
            ->where('jour_semaine', $donneesValidees['jour_semaine'])
            ->where('heure_debut', '<', $donneesValidees['heure_fin'])
            ->where('heure_fin', '>', $donneesValidees['heure_debut'])
            ->exists();

        if ($conflit) {
            return response()->json([
                'success' => false,
                'message' => 'Cet enseignant a déjà un cours sur ce créneau horaire.',
            ], 422);
        }

        $coursCree = Cours::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Cours créé avec succès !',
            'data' => $coursCree
        ], 201);
    }

    public function show(Cours $cours): JsonResponse
    {
        $cours->load(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle']);

        return response()->json([
            'success' => true,
            'message' => 'Détails du cours récupérés avec succès !',
            'data' => $cours
        ], 200);
    }

    public function edit(Cours $cours)
    {
        //
    }

    public function update(Request $request, Cours $cours): JsonResponse
    {
        $donneesValidees = $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => 'required|string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $enseignantId = \App\Models\Affectation::findOrFail($donneesValidees['affectation_id'])->enseignant_id;

        $conflit = Cours::whereHas('affectation', function ($query) use ($enseignantId) {
                $query->where('enseignant_id', $enseignantId);
            })
            ->where('jour_semaine', $donneesValidees['jour_semaine'])
            ->where('heure_debut', '<', $donneesValidees['heure_fin'])
            ->where('heure_fin', '>', $donneesValidees['heure_debut'])
            ->where('id', '!=', $cours->id)
            ->exists();

        if ($conflit) {
            return response()->json([
                'success' => false,
                'message' => 'Cet enseignant a déjà un cours sur ce créneau horaire.',
            ], 422);
        }

        $cours->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Cours modifié avec succès !',
            'data' => $cours
        ], 200);
    }

    public function destroy(Cours $cours): JsonResponse
    {
        $cours->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cours supprimé avec succès.'
        ], 200);
    }
}