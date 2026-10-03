<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnneeScolaireRequest;
use App\Http\Requests\UpdateAnneeScolaireRequest;
use App\Models\AnneeScolaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnneeScolaireController extends Controller
{
   
    /**
     * GET /api/annees-scolaires
     */
    public function index(Request $request): JsonResponse
    {
        $query = AnneeScolaire::query();

        if ($request->filled('en_cours')) {
            $query->where('en_cours', filter_var($request->en_cours, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where('libelle', 'like', '%' . $request->search . '%');
        }

        $annees = $query->orderByDesc('date_debut')->get();

        return response()->json([
            'success' => true,
            'data'    => $annees,
        ]);
    }

    /**
     * POST /api/annees-scolaires
     */
    public function store(StoreAnneeScolaireRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $enCours = $data['en_cours'] ?? false;

            if ($enCours) {
                AnneeScolaire::desactiverToutes();
            }

            $annee = AnneeScolaire::create([
                'libelle'    => $data['libelle'],
                'date_debut' => $data['date_debut'],
                'date_fin'   => $data['date_fin'],
                'en_cours'   => $enCours,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Année scolaire créée avec succès.",
                'data'    => $annee,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la création.",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/annees-scolaires/{id}
     */
    public function show(int $id): JsonResponse
    {
        $annee = AnneeScolaire::find($id);

        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Année scolaire introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $annee,
        ]);
    }

    /**
     * PUT/PATCH /api/annees-scolaires/{id}
     */
    public function update(UpdateAnneeScolaireRequest $request, int $id): JsonResponse
    {
        $annee = AnneeScolaire::find($id);

        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Année scolaire introuvable.",
            ], 404);
        }

        $data = $request->validated();

        DB::beginTransaction();
        try {
            if (!empty($data['en_cours'])) {
                AnneeScolaire::desactiverToutes();
            }

            $annee->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Année scolaire mise à jour.",
                'data'    => $annee->fresh(),
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
     * DELETE /api/annees-scolaires/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $annee = AnneeScolaire::find($id);

        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Année scolaire introuvable.",
            ], 404);
        }

        // Optionnel : empêcher la suppression si l'année est en cours
        if ($annee->en_cours) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer une année en cours.",
            ], 409);
        }

        $annee->delete();

        return response()->json([
            'success' => true,
            'message' => "Année scolaire supprimée.",
        ]);
    }

    /**
     * POST /api/annees-scolaires/{id}/activer
     */
    public function activer(int $id): JsonResponse
    {
        $annee = AnneeScolaire::find($id);

        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Année scolaire introuvable.",
            ], 404);
        }

        DB::beginTransaction();
        try {
            AnneeScolaire::desactiverToutes();
            $annee->update(['en_cours' => true]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Année scolaire activée.",
                'data'    => $annee->fresh(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'activation.",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/annees-scolaires/active
     */
    public function active(): JsonResponse
    {
        $annee = AnneeScolaire::enCours()->first();

        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Aucune année scolaire en cours.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $annee,
        ]);
    }
}
