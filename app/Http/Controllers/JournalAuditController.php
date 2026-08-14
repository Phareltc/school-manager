<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalAuditController extends Controller
{
    public function index(): JsonResponse
    {
        $journalAudits = JournalAudit::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des journaux d\'audit récupérée avec succès',
            'data' => $journalAudits
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        // Volontairement désactivé : un audit ne doit jamais pouvoir être créé manuellement
        // par un utilisateur via l'API. Il sera généré automatiquement par le système
        // (JournalAuditService, à construire plus tard) au moment des actions sensibles.
        return response()->json([
            'success' => false,
            'message' => 'La création manuelle d\'un journal d\'audit n\'est pas autorisée.',
        ], 403);
    }

    public function show(JournalAudit $journalAudit): JsonResponse
    {
        $journalAudit->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Détails du journal récupérés avec succès !',
            'data' => $journalAudit
        ], 200);
    }

    public function edit(JournalAudit $journalAudit)
    {
        //
    }

    public function update(Request $request, JournalAudit $journalAudit): JsonResponse
    {
        // Volontairement désactivé : un audit ne doit jamais être modifiable, sous peine
        // de perdre toute valeur probante.
        return response()->json([
            'success' => false,
            'message' => 'La modification d\'un journal d\'audit n\'est pas autorisée.',
        ], 403);
    }

    public function destroy(JournalAudit $journalAudit): JsonResponse
    {
        // Volontairement désactivé, même pour l'admin : un audit ne doit jamais être supprimable.
        return response()->json([
            'success' => false,
            'message' => 'La suppression d\'un journal d\'audit n\'est pas autorisée.',
        ], 403);
    }
}