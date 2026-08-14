<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Bulletin;
use App\Models\Enseignant;
use App\Models\Inscription;
use App\Services\BulletinService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    use ApiResponse;

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

        return $this->success('Liste des bulletins récupérée avec succès', $bulletins);
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

        return $this->success('Bulletin créé avec succès !', $bulletin, 201);
    }

    public function show(Request $request, Bulletin $bulletin): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $classeIds = $this->classesEnseigneesParUtilisateur($user);

            $classeActuelleEleve = Inscription::where('eleve_id', $bulletin->eleve_id)
                ->where('annee_scolaire_id', $bulletin->annee_scolaire_id)
                ->where('statut', 'actif')
                ->value('classe_id');

            if (!$classeActuelleEleve || !$classeIds->contains($classeActuelleEleve)) {
                return $this->error('Vous n\'êtes pas autorisé à consulter ce bulletin.', 403);
            }
        }

        $bulletin->load(['eleve', 'anneeScolaire']);

        return $this->success('Détails du bulletin récupérés avec succès !', $bulletin);
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

        return $this->success('Bulletin modifié avec succès !', $bulletin);
    }

    public function destroy(Bulletin $bulletin): JsonResponse
    {
        $bulletin->delete();

        return $this->success('Bulletin supprimé avec succès.');
    }

    protected function classesEnseigneesParUtilisateur($user)
    {
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        return $enseignant
            ? Affectation::where('enseignant_id', $enseignant->id)->pluck('classe_id')->unique()
            : collect();
    }
}