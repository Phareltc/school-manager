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
        $donneesValidees = $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $journalAudit = JournalAudit::create($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Action journalisée avec succès !',
            'data' => $journalAudit
        ], 201);
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
        $donneesValidees = $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $journalAudit->update($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Journal modifié avec succès !',
            'data' => $journalAudit
        ], 200);
    }

    public function destroy(JournalAudit $journalAudit): JsonResponse
    {
        $journalAudit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Journal supprimé avec succès.'
        ], 200);
    }
}