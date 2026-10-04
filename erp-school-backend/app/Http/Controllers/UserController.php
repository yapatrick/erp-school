<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Liste tous les utilisateurs avec leurs rôles et permissions.
     * GET /api/users
     */
    public function index(Request $request)
    {
        $query = User::with('roles.permissions');

        // Filtres optionnels
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('actif')) {
            $query->where('actif', $request->boolean('actif'));
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('slug', $request->input('role')));
        }

        $perPage = (int) $request->input('per_page', 15);
        $users = $query->orderBy('name')->paginate($perPage);

        return response()->json($users, 200);
    }

    /**
     * Crée un nouvel utilisateur.
     * POST /api/users
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:50|unique:users,name',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'actif'    => 'sometimes|boolean',
            'roles'    => 'sometimes|array',
            'roles.*'  => 'string|exists:roles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Création de l'utilisateur (mot de passe hashé auto via cast 'hashed')
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'actif'    => $data['actif'] ?? true,
        ]);

        // Assignation des rôles via pivot user_roles
        if (!empty($data['roles'])) {
            $roleIds = Role::whereIn('slug', $data['roles'])->pluck('id_role');
            $user->roles()->sync($roleIds);
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user'    => $user->load('roles.permissions'),
        ], 201);
    }

    /**
     * Affiche un utilisateur spécifique.
     * GET /api/users/{id}
     */
    public function show(string $id)
    {
        $user = User::with('roles.permissions')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        return response()->json([
            'user'        => $user,
            'permissions' => $user->getAllPermissions(), // bonus utile pour le frontend
        ], 200);
    }

    /**
     * Met à jour un utilisateur existant.
     * PUT /api/users/{id}
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => ['sometimes', 'string', 'max:50', Rule::unique('users', 'name')->ignore($user->id)],
            'email'    => ['sometimes', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|string|min:8|confirmed',
            'actif'    => 'sometimes|boolean',
            'roles'    => 'sometimes|array',
            'roles.*'  => 'string|exists:roles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Mise à jour des champs simples
        if (isset($data['name']))     $user->name = $data['name'];
        if (isset($data['email']))    $user->email = $data['email'];
        if (isset($data['actif']))    $user->actif = $data['actif'];
        if (!empty($data['password'])) $user->password = $data['password'];
        $user->save();

        // Synchronisation des rôles si fournis
        if (isset($data['roles'])) {
            $roleIds = Role::whereIn('slug', $data['roles'])->pluck('id_role');
            $user->roles()->sync($roleIds);
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'user'    => $user->fresh('roles.permissions'),
        ], 200);
    }

    /**
     * Supprime un utilisateur.
     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        // Empêcher l'auto-suppression
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 403);
        }

        // Détacher d'abord les rôles (la pivot a ON DELETE CASCADE, mais soyons explicites)
        $user->roles()->detach();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.'], 200);
    }

    // ==========================================
    // GESTION DES RÔLES
    // ==========================================

    /**
     * Assigne un ou plusieurs rôles à un utilisateur.
     * POST /api/users/{id}/roles
     */
    public function assignRoles(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'roles'   => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleIds = Role::whereIn('slug', $request->input('roles'))->pluck('id_role');

        // syncWithoutDetaching : ajoute sans retirer ceux existants
        $user->roles()->syncWithoutDetaching($roleIds);

        return response()->json([
            'message' => 'Rôles assignés avec succès.',
            'user'    => $user->fresh('roles.permissions'),
        ], 200);
    }

    /**
     * Retire un rôle spécifique à un utilisateur.
     * DELETE /api/users/{id}/roles/{roleSlug}
     */
    public function removeRole(string $id, string $roleSlug)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return response()->json(['message' => 'Rôle non trouvé.'], 404);
        }

        // Empêcher de retirer le dernier rôle d'un Admin (sécurité)
        if ($role->slug === 'Admin' && $user->roles()->count() === 1) {
            return response()->json([
                'message' => 'Impossible de retirer le dernier rôle Admin.'
            ], 403);
        }

        $user->roles()->detach($role->id_role);

        return response()->json([
            'message' => "Rôle '{$roleSlug}' retiré.",
            'user'    => $user->fresh('roles.permissions'),
        ], 200);
    }

    /**
     * Synchronise complètement les rôles d'un utilisateur (remplace).
     * PUT /api/users/{id}/roles
     */
    public function syncRoles(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'roles'   => 'present|array',
            'roles.*' => 'string|exists:roles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleIds = Role::whereIn('slug', $request->input('roles', []))->pluck('id_role');
        $user->roles()->sync($roleIds);

        return response()->json([
            'message' => 'Rôles synchronisés avec succès.',
            'user'    => $user->fresh('roles.permissions'),
        ], 200);
    }

    // ==========================================
    // STATISTIQUES / BONUS
    // ==========================================

    /**
     * Renvoie les permissions effectives d'un utilisateur (aplaties).
     * GET /api/users/{id}/permissions
     */
    public function permissions(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        return response()->json([
            'user_id'     => $user->id,
            'roles'       => $user->roles()->pluck('slug'),
            'permissions' => $user->getAllPermissions(),
        ], 200);
    }
}