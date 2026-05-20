<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NiveauController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Quand le client tape /api/niveaux avec la méthode GET, on appelle la fonction 'index' du NiveauController
Route::get('/niveaux', [NiveauController::class, 'index']);