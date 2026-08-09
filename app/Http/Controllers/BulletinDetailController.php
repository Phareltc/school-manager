<?php

namespace App\Http\Controllers;

use App\Models\BulletinDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinDetailController extends Controller
{
    public function index(): JsonResponse
    {
        $bulletinDetails = BulletinDetail::with(['bulletin', 'matiere'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des détails de bulletin récupérée avec succès',
            'data' => $bulletinDetails
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'bulletin_id' => 'required|exists:bulletins,id',
            'matiere_id' => 'required|exists:matieres,id',
            'moyenne_matiere' => 'required|numeric|min:0|max:20',
            'appreciation_enseignant' => 'required|string',
        ]);

        // Règle métier : un bulletin ne peut avoir qu'une seule ligne par matière
        $dejaDetail = BulletinDetail::where('bulletin_id', $donneesValidees['bulletin_id'])
            ->where('matiere_id', $donneesValidees['matiere_id'])
            ->exists();

        if ($dejaDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Ce bulletin possède déjà une ligne pour cette matière.',
            ], 422);
        }

        $bulletinDetail = BulletinDetail::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Détail de bulletin créé avec succès !',
            'data' => $bulletinDetail
        ], 201);
    }

    public function show(BulletinDetail $bulletinDetail): JsonResponse
    {
        $bulletinDetail->load(['bulletin', 'matiere']);

        return response()->json([
            'success' => true,
            'message' => 'Détails récupérés avec succès !',
            'data' => $bulletinDetail
        ], 200);
    }

    public function edit(BulletinDetail $bulletinDetail)
    {
        //
    }

    public function update(Request $request, BulletinDetail $bulletinDetail): JsonResponse
    {
        $donneesValidees = $request->validate([
            'bulletin_id' => 'required|exists:bulletins,id',
            'matiere_id' => 'required|exists:matieres,id',
            'moyenne_matiere' => 'required|numeric|min:0|max:20',
            'appreciation_enseignant' => 'required|string',
        ]);

        $dejaDetail = BulletinDetail::where('bulletin_id', $donneesValidees['bulletin_id'])
            ->where('matiere_id', $donneesValidees['matiere_id'])
            ->where('id', '!=', $bulletinDetail->id)
            ->exists();

        if ($dejaDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Ce bulletin possède déjà une ligne pour cette matière.',
            ], 422);
        }

        $bulletinDetail->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Détail de bulletin modifié avec succès !',
            'data' => $bulletinDetail
        ], 200);
    }

    public function destroy(BulletinDetail $bulletinDetail): JsonResponse
    {
        $bulletinDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Détail de bulletin supprimé avec succès.'
        ], 200);
    }
}