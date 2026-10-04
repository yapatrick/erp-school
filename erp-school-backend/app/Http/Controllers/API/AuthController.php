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
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants invalides.'], 401); // ← 401 plus standard que 411
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que le compte est actif
        if (!$user->actif) {
            Auth::logout();
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        // Mettre à jour la dernière connexion
        $user->update(['derniere_connexion' => now()]);

        // Récupérer les slugs des rôles pour les scopes
        $scopes = $user->roles()->pluck('slug')->toArray();

        // Créer le token Passport
        $tokenResult = $user->createToken('ERP_School_Token', $scopes);

        return response()->json([
            'message'      => 'Connexion réussie',
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $tokenResult->token->expires_at,
            'user'         => $user->load('roles.permissions'),
        ], 200);
    }


    /**
     * Déconnexion de l'utilisateur — révoque le token d'accès courant.
     */
    public function logoutJWT(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        // Récupérer le token courant et le révoquer
        $token = $user->token();

        if ($token) {
            $token->revoke();
            // Optionnel : révoquer aussi les refresh tokens liés
            $token->refreshToken?->revoke();
        }

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ], 200);
    }

    /**
     * Rafraîchir le token d'accès (Refresh Token)
     */
    public function refresh(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        // Révoquer les anciens tokens de l'utilisateur pour forcer la rotation
        $user->tokens()->where('revoked', false)->each(function ($token) {
            $token->revoke();
        });

        // Recréer les scopes actuels
        $scopes = $user->roles()->pluck('slug')->toArray();

        // Générer un nouveau token Passport
        $tokenResult = $user->createToken('ERP_School_Token', $scopes);

        return response()->json([
            'message'      => 'Token rafraîchi avec succès',
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $tokenResult->token->expires_at,
            'user'         => $user->load('roles.permissions'),
        ], 200);
    }

    public function meJWT(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        $roles = $user->roles()->with('permissions')->get();

        $permissions = $roles
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'message' => 'Informations utilisateur',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'actif' => (bool) $user->actif,
                'derniere_connexion' => $user->derniere_connexion,
                'roles' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                    ];
                }),
                'permissions' => $permissions,
            ],
        ], 200);
    }
}
