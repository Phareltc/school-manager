<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Enseignant;
use App\Services\CoursService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    use ApiResponse;

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

            $cours = $enseignant
                ? Cours::with(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle'])
                    ->whereHas('affectation', function ($query) use ($enseignant) {
                        $query->where('enseignant_id', $enseignant->id);
                    })
                    ->get()
                : collect();
        }

        return $this->success('Liste des cours récupérée avec succès', $cours);
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

        return $this->success('Cours créé avec succès !', $coursCree, 201);
    }

    public function show(Request $request, Cours $cours): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            $cours->load('affectation');

            if (!$enseignant || $cours->affectation->enseignant_id !== $enseignant->id) {
                return $this->error('Vous n\'êtes pas autorisé à consulter ce cours.', 403);
            }
        }

        $cours->load(['affectation.enseignant.user', 'affectation.classe', 'affectation.matiere', 'salle']);

        return $this->success('Détails du cours récupérés avec succès !', $cours);
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

        return $this->success('Cours modifié avec succès !', $cours);
    }

    public function destroy(Cours $cours): JsonResponse
    {
        $cours->delete();

        return $this->success('Cours supprimé avec succès.');
    }
}