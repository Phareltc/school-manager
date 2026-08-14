<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Enseignant;
use App\Services\CoursService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    public function __construct(
        protected CoursService $coursService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $cours = Cours::with(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle'])->get();
        } else {
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            // whereHas filtre les cours dont l'affectation liée appartient à CET enseignant
            $cours = $enseignant
                ? Cours::with(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle'])
                    ->whereHas('affectation', function ($query) use ($enseignant) {
                        $query->where('enseignant_id', $enseignant->id);
                    })
                    ->get()
                : collect();
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des cours récupérée avec succès',
            'data' => $cours
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => 'required|string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $coursCree = $this->coursService->creer($donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Cours créé avec succès !',
            'data' => $coursCree
        ], 201);
    }

    public function show(Request $request, Cours $cours): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            // On charge l'affectation pour comparer son enseignant_id
            $cours->load('affectation');

            if (!$enseignant || $cours->affectation->enseignant_id !== $enseignant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à consulter ce cours.',
                ], 403);
            }
        }

        $cours->load(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle']);

        return response()->json([
            'success' => true,
            'message' => 'Détails du cours récupérés avec succès !',
            'data' => $cours
        ], 200);
    }

    public function edit(Cours $cours)
    {
        //
    }

    public function update(Request $request, Cours $cours): JsonResponse
    {
        $donneesValidees = $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'salle_id' => 'required|exists:salles,id',
            'jour_semaine' => 'required|string|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $cours = $this->coursService->modifier($cours, $donneesValidees);

        return response()->json([
            'success' => true,
            'message' => 'Cours modifié avec succès !',
            'data' => $cours
        ], 200);
    }

    public function destroy(Cours $cours): JsonResponse
    {
        $cours->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cours supprimé avec succès.'
        ], 200);
    }
}