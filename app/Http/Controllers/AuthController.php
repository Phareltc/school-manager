<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion : vérifie email/mot de passe, retourne un token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $donneesValidees = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $donneesValidees['email'])->first();

        // On vérifie à la fois que l'utilisateur existe ET que le mot de passe correspond
        if (!$user || !Auth::attempt($donneesValidees)) {
            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }

        // createToken() génère un nouveau token Sanctum, avec un nom pour l'identifier
        // (utile si un utilisateur a plusieurs tokens : mobile, web, etc.)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie !',
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Déconnexion : supprime le token utilisé pour cette requête.
     */
    public function logout(Request $request): JsonResponse
    {
        // currentAccessToken() récupère le token précis utilisé pour authentifier CETTE requête,
        // et on le supprime — les autres tokens de l'utilisateur (autres appareils) restent valides.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ], 200);
    }

    /**
     * Retourne l'utilisateur actuellement authentifié (utile pour un frontend qui veut
     * vérifier qui est connecté au chargement de l'app).
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur authentifié récupéré avec succès',
            'data' => [
                'user' => $request->user(),
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            ]
        ], 200);
    }
}