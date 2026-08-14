<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\BulletinDetail;
use App\Models\Enseignant;
use App\Models\Inscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinDetailController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $bulletinDetails = BulletinDetail::with(['bulletin.eleve', 'matiere'])->get();
        } else {
            $classeIds = $this->classesEnseigneesParUtilisateur($user);

            $bulletinDetails = $classeIds->isEmpty()
                ? collect()
                : BulletinDetail::with(['bulletin.eleve', 'matiere'])
                    ->whereHas('bulletin.eleve.inscriptions', fn ($q) => $q
                        ->whereIn('classe_id', $classeIds)
                        ->where('statut', 'actif'))
                    ->get();
        }

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

    public function show(Request $request, BulletinDetail $bulletinDetail): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $bulletinDetail->load('bulletin');
            $classeIds = $this->classesEnseigneesParUtilisateur($user);

            $classeActuelleEleve = Inscription::where('eleve_id', $bulletinDetail->bulletin->eleve_id)
                ->where('annee_scolaire_id', $bulletinDetail->bulletin->annee_scolaire_id)
                ->where('statut', 'actif')
                ->value('classe_id');

            if (!$classeActuelleEleve || !$classeIds->contains($classeActuelleEleve)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à consulter ce détail de bulletin.',
                ], 403);
            }
        }

        $bulletinDetail->load(['bulletin.eleve', 'matiere']);

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

    protected function classesEnseigneesParUtilisateur($user)
    {
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        return $enseignant
            ? Affectation::where('enseignant_id', $enseignant->id)->pluck('classe_id')->unique()
            : collect();
    }
}