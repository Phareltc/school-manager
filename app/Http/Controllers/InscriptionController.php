<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $inscriptions = Inscription::with(['eleve', 'classe', 'anneeScolaire'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des inscriptions récupérée avec succès',
            'data' => $inscriptions
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
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'date_inscription' => 'required|date',
            'statut' => 'in:actif,transféré,abandonné',
        ]);

        // Règle métier : un élève ne peut avoir qu'une seule inscription ACTIVE par année scolaire.
        // On ne bloque que si la nouvelle inscription est elle-même "active" (ou pas précisée, car
        // le statut par défaut en base est "actif").
        $statutDemande = $donneesValidees['statut'] ?? 'actif';

        if ($statutDemande === 'actif') {
            $dejaInscritActif = Inscription::where('eleve_id', $donneesValidees['eleve_id'])
                ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
                ->where('statut', 'actif')
                ->exists();

            if ($dejaInscritActif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet élève possède déjà une inscription active pour cette année scolaire.',
                ], 422);
            }
        }

        $inscription = Inscription::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Inscription créée avec succès !',
            'data' => $inscription
        ], 201);
    }

    public function show(Inscription $inscription): JsonResponse
    {
        $inscription->load(['eleve', 'classe', 'anneeScolaire']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de l\'inscription récupérés avec succès !',
            'data' => $inscription
        ], 200);
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

        $statutDemande = $donneesValidees['statut'] ?? 'actif';

        if ($statutDemande === 'actif') {
            $dejaInscritActif = Inscription::where('eleve_id', $donneesValidees['eleve_id'])
                ->where('annee_scolaire_id', $donneesValidees['annee_scolaire_id'])
                ->where('statut', 'actif')
                ->where('id', '!=', $inscription->id)
                ->exists();

            if ($dejaInscritActif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet élève possède déjà une inscription active pour cette année scolaire.',
                ], 422);
            }
        }

        $inscription->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Inscription modifiée avec succès !',
            'data' => $inscription
        ], 200);
    }

    public function destroy(Inscription $inscription): JsonResponse
    {
        $inscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscription supprimée avec succès.'
        ], 200);
    }
}