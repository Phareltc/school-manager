<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Enseignant;
use App\Services\AffectationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AffectationService $affectationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $affectations = Affectation::with(['enseignant.user', 'classe', 'matiere', 'anneeScolaire'])->get();
        } else {
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            $affectations = $enseignant
                ? Affectation::with(['enseignant.user', 'classe', 'matiere', 'anneeScolaire'])
                    ->where('enseignant_id', $enseignant->id)
                    ->get()
                : collect();
        }

        return $this->success('Liste des affectations récupérée avec succès', $affectations);
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

        return $this->success('Affectation créée avec succès !', $affectation, 201);
    }

    public function show(Request $request, Affectation $affectation): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            if (!$enseignant || $affectation->enseignant_id !== $enseignant->id) {
                return $this->error('Vous n\'êtes pas autorisé à consulter cette affectation.', 403);
            }
        }

        $affectation->load(['enseignant.user', 'classe', 'matiere', 'anneeScolaire']);

        return $this->success('Détails de l\'affectation récupérés avec succès !', $affectation);
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

        return $this->success('Affectation modifiée avec succès !', $affectation);
    }

    public function destroy(Affectation $affectation): JsonResponse
    {
        $affectation->delete();

        return $this->success('Affectation supprimée avec succès.');
    }
}