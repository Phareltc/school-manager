<?php

use App\Http\Controllers\EleveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NiveauController;

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


Route::post('/eleves', [EleveController::class, 'store']);
Route::get('/eleves/{id}', [EleveController::class, 'show']);
Route::put('/eleves/{id}', [EleveController::class, 'update']);