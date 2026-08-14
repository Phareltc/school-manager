<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Services\InscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected InscriptionService $inscriptionService
    ) {}

    public function index(): JsonResponse
    {
        $inscriptions = Inscription::with(['eleve', 'classe', 'anneeScolaire'])->get();

        return $this->success('Liste des inscriptions récupérée avec succès', $inscriptions);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'date_inscription' => 'required|date',
            'statut' => 'in:actif,transféré,abandonné',
        ]);

        $inscription = $this->inscriptionService->creer($donneesValidees);

        return $this->success('Inscription créée avec succès !', $inscription, 201);
    }

    public function show(Inscription $inscription): JsonResponse
    {
        $inscription->load(['eleve', 'classe', 'anneeScolaire']);

        return $this->success('Détails de l\'inscription récupérés avec succès !', $inscription);
    }

    public function edit(Inscription $inscription)
    {
        //
    }

    public function update(Request $request, Inscription $inscription): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'date_inscription' => 'required|date',
            'statut' => 'in:actif,transféré,abandonné',
        ]);

        $inscription = $this->inscriptionService->modifier($inscription, $donneesValidees);

        return $this->success('Inscription modifiée avec succès !', $inscription);
    }

    public function destroy(Inscription $inscription): JsonResponse
    {
        $inscription->delete();

        return $this->success('Inscription supprimée avec succès.');
    }
}