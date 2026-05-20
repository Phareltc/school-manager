<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // 1. On demande au modèle d'aller chercher tous les niveaux en base PostgreSQL
        $niveaux = Niveau::all();

        // 2. Le serveur répond au client avec un statut 200 (OK) et les données au format JSON
        return response()->json([
            'success' => true,
            'message' => 'Liste des niveaux récupérée avec succès',
            'data' => $niveaux
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Niveau $niveau)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Niveau $niveau)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Niveau $niveau)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Niveau $niveau)
    {
        //
    }
}
