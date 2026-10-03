<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypePaiementRequest;
use App\Http\Requests\UpdateTypePaiementRequest;
use App\Models\TypePaiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypePaiementController extends Controller
{
    /**
     * GET /api/types-paiement
     */
    public function index(Request $request): JsonResponse
    {
        $query = TypePaiement::query()->withCount('paiements');

        if ($request->filled('actif')) {
            $query->where('actif', filter_var($request->actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('libelle', 'like', '%' . $request->search . '%')
                  ->orWhere('code_type', 'like', '%' . $request->search . '%');
            });
        }

        $types = $query->orderBy('libelle')->get();

        return response()->json([
            'success' => true,
            'data'    => $types,
        ]);
    }

    /**
     * POST /api/types-paiement
     */
    public function store(StoreTypePaiementRequest $request): JsonResponse
    {
        $type = TypePaiement::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Type de paiement créé avec succès.",
            'data'    => $type,
        ], 201);
    }

    /**
     * GET /api/types-paiement/{id}
     */
    public function show(int $id): JsonResponse
    {
        $type = TypePaiement::withCount('paiements')->find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => "Type de paiement introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $type,
        ]);
    }

    /**
     * PUT/PATCH /api/types-paiement/{id}
     */
    public function update(UpdateTypePaiementRequest $request, int $id): JsonResponse
    {
        $type = TypePaiement::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => "Type de paiement introuvable.",
            ], 404);
        }

        $type->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Type de paiement mis à jour.",
            'data'    => $type->fresh(),
        ]);
    }

    /**
     * DELETE /api/types-paiement/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $type = TypePaiement::withCount('paiements')->find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => "Type de paiement introuvable.",
            ], 404);
        }

        // Empêcher la suppression si des paiements y sont rattachés
        if ($type->paiements_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$type->paiements_count} paiement(s) utilisent ce type.",
            ], 409);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => "Type de paiement supprimé.",
        ]);
    }

    /**
     * POST /api/types-paiement/{id}/toggle
     */
    public function toggle(int $id): JsonResponse
    {
        $type = TypePaiement::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => "Type de paiement introuvable.",
            ], 404);
        }

        $type->update(['actif' => !$type->actif]);

        return response()->json([
            'success' => true,
            'message' => $type->actif
                ? "Type de paiement activé."
                : "Type de paiement désactivé.",
            'data'    => $type->fresh(),
        ]);
    }
}