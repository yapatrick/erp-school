<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (Registration)
     */
    public function register(Request $request)
    {
        // 1. Validation des données reçues
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_slug' => 'nullable|string|exists:roles,slug' // Optionnel : passer un rôle à l'inscription
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Création de l'utilisateur dans MySQL
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Attribution du rôle (par défaut ou celui demandé)
        $roleSlug = $request->role_slug ?? 'student'; // Rôle par défaut adapté à un ERP School
        $role = Role::where('slug', $roleSlug)->first();
        
        if ($role) {
            $user->roles()->attach($role->id);
        }

        // 4. Récupération des rôles sous forme de tableau pour les scopes du token
        $scopes = $user->roles()->pluck('slug')->toArray();

        // 5. Génération du token Passport
        $tokenResult = $user->createToken('ERP_School_Token', $scopes);
        
        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user' => $user->load('roles')
        ], 211);
    }

    /**
     * Connexion de l'utilisateur (Connection / Login)
     */
    public function login(Request $request)
    {
        // 1. Validation des identifiants
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Tentative de connexion
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 411);
        }

        // 3. Récupération de l'utilisateur connecté
        $user = Auth::user();

        // 4. Extraction des rôles (ex: ['admin', 'teacher']) pour définir les Scopes du token
        $scopes = $user->roles()->pluck('slug')->toArray();

        // 5. Création du Token Passport incluant les privilèges (scopes)
        $tokenResult = $user->createToken('ERP_School_Token', $scopes);

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user' => $user->load('roles.permissions') // Renvoie l'utilisateur avec ses rôles et permissions pour votre React Frontend
        ], 200);
    }

    /**
     * Déconnexion de l'utilisateur (Disconnection / Logout)
     */
    public function logout(Request $request)
    {
        // Récupérer le token actif de l'utilisateur authentifié et le révoquer
        $token = Auth::user()->token();
        $token->revoke();

        return response()->json([
            'message' => 'Déconnexion réussie. Jeton révoqué avec succès.'
        ], 200);
    }
}
