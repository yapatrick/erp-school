<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * GET /api/permissions
     * Filtres possibles : categorie, actif, search, group_by=categorie
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permission::query();

        if ($request->filled('categorie')) {
            $query->parCategorie($request->categorie);
        }

        if ($request->filled('actif')) {
            $query->where('actif', filter_var($request->actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $permissions = $query->orderBy('categorie')->orderBy('nom')->get();

        // Option : regrouper par catégorie
        if ($request->boolean('group_by') === true || $request->get('group_by') === 'categorie') {
            return response()->json([
                'success' => true,
                'data'    => $permissions->groupBy('categorie'),
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $permissions,
        ]);
    }

    /**
     * GET /api/permissions/categories
     * Renvoie la liste des catégories distinctes
     */
    public function categories(): JsonResponse
    {
        $categories = Permission::query()
            ->select('categorie')
            ->distinct()
            ->orderBy('categorie')
            ->pluck('categorie');

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * POST /api/permissions
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $permission = Permission::create([
            'nom'         => $data['nom'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'categorie'   => $data['categorie'],
            'actif'       => $data['actif'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Permission créée avec succès.",
            'data'    => $permission,
        ], 201);
    }

    /**
     * GET /api/permissions/{id}
     */
    public function show(int $id): JsonResponse
    {
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => "Permission introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $permission,
        ]);
    }

    /**
     * PUT/PATCH /api/permissions/{id}
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => "Permission introuvable.",
            ], 404);
        }

        $permission->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Permission mise à jour.",
            'data'    => $permission->fresh(),
        ]);
    }

    /**
     * DELETE /api/permissions/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $permission = Permission::withCount('roles')->find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => "Permission introuvable.",
            ], 404);
        }

        // Empêcher la suppression si la permission est attachée à des rôles
        if ($permission->roles_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : cette permission est assignée à {$permission->roles_count} rôle(s).",
            ], 409);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => "Permission supprimée.",
        ]);
    }

    /**
     * POST /api/permissions/{id}/toggle
     * Activer / désactiver rapidement une permission
     */
    public function toggle(int $id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => "Permission introuvable.",
            ], 404);
        }

        $permission->update(['actif' => !$permission->actif]);

        return response()->json([
            'success' => true,
            'message' => $permission->actif
                ? "Permission activée."
                : "Permission désactivée.",
            'data'    => $permission->fresh(),
        ]);
    }
}