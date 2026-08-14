<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Enseignant;
use App\Services\AffectationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function __construct(
        protected AffectationService $affectationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            // L'admin voit toutes les affectations
            $affectations = Affectation::with(['enseignant.user', 'classe', 'matiere', 'anneeScolaire'])->get();
        } else {
            // On retrouve l'enseignant lié à CET utilisateur authentifié, jamais depuis une donnée envoyée par le client
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            // Si l'utilisateur connecté n'est pas lié à une fiche enseignant, il n'a aucune affectation à voir
            $affectations = $enseignant
                ? Affectation::with(['enseignant.user', 'classe', 'matiere', 'anneeScolaire'])
                    ->where('enseignant_id', $enseignant->id)
                    ->get()
                : collect();
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des affectations récupérée avec succès',
            'data' => $affectations
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'charge_horaire_hebdomadaire' => 'required|integer|min:1',
        ]);

        $affectation = $this->affectationService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Affectation créée avec succès !',
            'data' => $affectation
        ], 201);
    }

    public function show(Request $request, Affectation $affectation): JsonResponse
    {
        $user = $request->user();

        // Un enseignant ne peut consulter QUE ses propres affectations, même via l'URL directe /affectations/{id}
        if (!$user->hasRole('admin')) {
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            if (!$enseignant || $affectation->enseignant_id !== $enseignant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à consulter cette affectation.',
                ], 403);
            }
        }

        $affectation->load(['enseignant.user', 'classe', 'matiere', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'affectation récupérés avec succès !',
            'data' => $affectation
        ], 200);
    }

    public function edit(Affectation $affectation)
    {
        //
    }

    public function update(Request $request, Affectation $affectation): JsonResponse
    {
        $donneesValidees = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'charge_horaire_hebdomadaire' => 'required|integer|min:1',
        ]);

        $affectation = $this->affectationService->modifier($affectation, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Affectation modifiée avec succès !',
            'data' => $affectation
        ], 200);
    }

    public function destroy(Affectation $affectation): JsonResponse
    {
        $affectation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Affectation supprimée avec succès.'
        ], 200);
    }
}