<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalAuditController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $journalAudits = JournalAudit::with('user')->get();

        return $this->success('Liste des journaux d\'audit récupérée avec succès', $journalAudits);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        return $this->error('La création manuelle d\'un journal d\'audit n\'est pas autorisée.', 403);
    }

    public function show(JournalAudit $journalAudit): JsonResponse
    {
        $journalAudit->load('user');

        return $this->success('Détails du journal récupérés avec succès !', $journalAudit);
    }

    public function edit(JournalAudit $journalAudit)
    {
        //
    }

    public function update(Request $request, JournalAudit $journalAudit): JsonResponse
    {
        return $this->error('La modification d\'un journal d\'audit n\'est pas autorisée.', 403);
    }

    public function destroy(JournalAudit $journalAudit): JsonResponse
    {
        return $this->error('La suppression d\'un journal d\'audit n\'est pas autorisée.', 403);
    }
}