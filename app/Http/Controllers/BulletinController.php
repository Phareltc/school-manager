<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Bulletin;
use App\Models\Enseignant;
use App\Services\BulletinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function __construct(
        protected BulletinService $bulletinService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $bulletins = Bulletin::with(['eleve', 'anneeScolaire'])->get();
        } else {
            $classeIds = $this->classesEnseigneesParUtilisateur($user);

            $bulletins = $classeIds->isEmpty()
                ? collect()
                : Bulletin::with(['eleve', 'anneeScolaire'])
                    ->whereHas('eleve.inscriptions', fn ($q) => $q
                        ->whereColumn('inscriptions.annee_scolaire_id', 'bulletins.annee_scolaire_id')
                        ->whereIn('classe_id', $classeIds)
                        ->where('statut', 'actif'))
                    ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des bulletins récupérée avec succès',
            'data' => $bulletins
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'rang' => 'required|integer|min:1',
            'appreciation' => 'required|string',
        ]);

        $bulletin = $this->bulletinService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Bulletin créé avec succès !',
            'data' => $bulletin
        ], 201);
    }

    public function show(Request $request, Bulletin $bulletin): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $classeIds = $this->classesEnseigneesParUtilisateur($user);

            $classeActuelleEleve = \App\Models\Inscription::where('eleve_id', $bulletin->eleve_id)
                ->where('annee_scolaire_id', $bulletin->annee_scolaire_id)
                ->where('statut', 'actif')
                ->value('classe_id');

            if (!$classeActuelleEleve || !$classeIds->contains($classeActuelleEleve)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à consulter ce bulletin.',
                ], 403);
            }
        }

        $bulletin->load(['eleve', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails du bulletin récupérés avec succès !',
            'data' => $bulletin
        ], 200);
    }

    public function edit(Bulletin $bulletin)
    {
        //
    }

    public function update(Request $request, Bulletin $bulletin): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'rang' => 'required|integer|min:1',
            'appreciation' => 'required|string',
        ]);

        $bulletin = $this->bulletinService->modifier($bulletin, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Bulletin modifié avec succès !',
            'data' => $bulletin
        ], 200);
    }

    public function destroy(Bulletin $bulletin): JsonResponse
    {
        $bulletin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bulletin supprimé avec succès.'
        ], 200);
    }

    /**
     * Retourne les IDs des classes où l'enseignant connecté a au moins une affectation
     * (peu importe la matière) — sert à déterminer quels bulletins d'élèves il peut consulter.
     */
    protected function classesEnseigneesParUtilisateur($user)
    {
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        return $enseignant
            ? Affectation::where('enseignant_id', $enseignant->id)->pluck('classe_id')->unique()
            : collect();
    }
}