<?php

use App\Http\Controllers\EleveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\FiliereController;

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