<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * GET /api/roles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::withCount('users')->with('permissions');

        if ($request->filled('actif')) {
            $query->where('actif', filter_var($request->actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomRole', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $roles = $query->orderBy('nomRole')->get();

        return response()->json([
            'success' => true,
            'data'    => $roles,
        ]);
    }

    /**
     * POST /api/roles
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $role = Role::create([
                'nomRole'     => $data['nomRole'],
                'slug'        => $data['slug'],
                'description' => $data['description'] ?? null,
                'actif'       => $data['actif'] ?? true,
            ]);

            if (!empty($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Rôle créé avec succès.",
                'data'    => $role->load('permissions'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la création du rôle.",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/roles/{id}
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::with(['permissions', 'users'])->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $role,
        ]);
    }

    /**
     * PUT/PATCH /api/roles/{id}
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        $data = $request->validated();

        DB::beginTransaction();
        try {
            // Protéger le rôle admin : on ne peut pas le désactiver
            if ($role->slug === 'admin' && isset($data['actif']) && $data['actif'] === false) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Le rôle administrateur ne peut pas être désactivé.",
                ], 403);
            }

            $role->update(collect($data)->only(['nomRole', 'slug', 'description', 'actif'])->toArray());

            if (array_key_exists('permissions', $data)) {
                $role->permissions()->sync($data['permissions'] ?? []);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Rôle mis à jour.",
                'data'    => $role->fresh()->load('permissions'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la mise à jour.",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/roles/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $role = Role::withCount('users')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        // Rôle admin : suppression interdite
        if ($role->slug === 'admin') {
            return response()->json([
                'success' => false,
                'message' => "Le rôle administrateur ne peut pas être supprimé.",
            ], 403);
        }

        // Rôle assigné à des utilisateurs
        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : ce rôle est assigné à {$role->users_count} utilisateur(s).",
            ], 409);
        }

        DB::beginTransaction();
        try {
            $role->permissions()->detach();
            $role->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Rôle supprimé.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la suppression.",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/roles/{id}/permissions
     * Assigner un ensemble de permissions au rôle
     * Body: { "permissions": [1, 2, 3] }
     */
    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id_permission'],
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'success' => true,
            'message' => "Permissions mises à jour.",
            'data'    => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * POST /api/roles/{id}/permissions/add
     * Ajouter des permissions sans retirer les existantes
     */
    public function addPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id_permission'],
        ]);

        $role->permissions()->syncWithoutDetaching($request->permissions);

        return response()->json([
            'success' => true,
            'message' => "Permissions ajoutées.",
            'data'    => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * DELETE /api/roles/{id}/permissions/{idPermission}
     */
    public function removePermission(int $id, int $idPermission): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Rôle introuvable.",
            ], 404);
        }

        $role->permissions()->detach($idPermission);

        return response()->json([
            'success' => true,
            'message' => "Permission retirée du rôle.",
            'data'    => $role->fresh()->load('permissions'),
        ]);
    }
}