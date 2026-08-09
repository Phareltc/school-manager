<?php

use App\Http\Controllers\EleveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\InscriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Quand le client tape /api/niveaux avec la méthode GET, on appelle la fonction 'index' du NiveauController
Route::get('/niveaux', [NiveauController::class, 'index']);
Route::post('/niveaux', [NiveauController::class, 'store']);
// ATTENTION : Ici on écrit {niveau} pour correspondre à Niveau $niveau
Route::get('/niveaux/{niveau}', [NiveauController::class, 'show']);
Route::put('/niveaux/{niveau}', [NiveauController::class, 'update']);
Route::delete('/niveaux/{niveau}', [NiveauController::class, 'destroy']);

Route::get('/eleves', [EleveController::class, 'index']);
Route::post('/eleves', [EleveController::class, 'store']);
Route::get('/eleves/{eleve}', [EleveController::class, 'show']);
Route::put('/eleves/{eleve}', [EleveController::class, 'update']);
Route::delete('/eleves/{eleve}', [EleveController::class, 'destroy']);

Route::get('/annees-scolaires', [AnneeScolaireController::class, 'index']);
Route::post('/annees-scolaires', [AnneeScolaireController::class, 'store']);
Route::get('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'show']);
Route::put('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'update']);
Route::delete('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'destroy']);

Route::get('/filieres', [FiliereController::class, 'index']);
Route::post('/filieres', [FiliereController::class, 'store']);
Route::get('/filieres/{filiere}', [FiliereController::class, 'show']);
Route::put('/filieres/{filiere}', [FiliereController::class, 'update']);
Route::delete('/filieres/{filiere}', [FiliereController::class, 'destroy']);

Route::get('/classes', [ClasseController::class, 'index']);
Route::post('/classes', [ClasseController::class, 'store']);
Route::get('/classes/{classe}', [ClasseController::class, 'show']);
Route::put('/classes/{classe}', [ClasseController::class, 'update']);
Route::delete('/classes/{classe}', [ClasseController::class, 'destroy']);

Route::get('/matieres', [MatiereController::class, 'index']);
Route::post('/matieres', [MatiereController::class, 'store']);
Route::get('/matieres/{matiere}', [MatiereController::class, 'show']);
Route::put('/matieres/{matiere}', [MatiereController::class, 'update']);
Route::delete('/matieres/{matiere}', [MatiereController::class, 'destroy']);

Route::get('/salles', [SalleController::class, 'index']);
Route::post('/salles', [SalleController::class, 'store']);
Route::get('/salles/{salle}', [SalleController::class, 'show']);
Route::put('/salles/{salle}', [SalleController::class, 'update']);
Route::delete('/salles/{salle}', [SalleController::class, 'destroy']);

Route::get('/enseignants', [EnseignantController::class, 'index']);
Route::post('/enseignants', [EnseignantController::class, 'store']);
Route::get('/enseignants/{enseignant}', [EnseignantController::class, 'show']);
Route::put('/enseignants/{enseignant}', [EnseignantController::class, 'update']);
Route::delete('/enseignants/{enseignant}', [EnseignantController::class, 'destroy']);

Route::get('/affectations', [AffectationController::class, 'index']);
Route::post('/affectations', [AffectationController::class, 'store']);
Route::get('/affectations/{affectation}', [AffectationController::class, 'show']);
Route::put('/affectations/{affectation}', [AffectationController::class, 'update']);
Route::delete('/affectations/{affectation}', [AffectationController::class, 'destroy']);

Route::get('/inscriptions', [InscriptionController::class, 'index']);
Route::post('/inscriptions', [InscriptionController::class, 'store']);
Route::get('/inscriptions/{inscription}', [InscriptionController::class, 'show']);
Route::put('/inscriptions/{inscription}', [InscriptionController::class, 'update']);
Route::delete('/inscriptions/{inscription}', [InscriptionController::class, 'destroy']);