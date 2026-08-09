<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(
        protected NoteService $noteService
    ) {}

    public function index(): JsonResponse
    {
        $notes = Note::with(['eleve', 'examen', 'matiere', 'enseignant.user'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des notes récupérée avec succès',
            'data' => $notes
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
            'examen_id' => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        $note = $this->noteService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Note créée avec succès !',
            'data' => $note
        ], 201);
    }

    public function show(Note $note): JsonResponse
    {
        $note->load(['eleve', 'examen', 'matiere', 'enseignant.user']);

        return response()->json([
            'success' => true,
            'message' => 'Détails de la note récupérés avec succès !',
            'data' => $note
        ], 200);
    }

    public function edit(Note $note)
    {
        //
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        $donneesValidees = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'examen_id' => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'note' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string',
        ]);

        $note = $this->noteService->modifier($note, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Note modifiée avec succès !',
            'data' => $note
        ], 200);
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note supprimée avec succès.'
        ], 200);
    }
}