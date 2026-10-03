<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePeriodeEvaluationRequest;
use App\Http\Requests\UpdatePeriodeEvaluationRequest;
use App\Models\PeriodeEvaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeEvaluationController extends Controller
{
    /**
     * GET /api/periodes-evaluation
     */
    public function index(Request $request): JsonResponse
    {
        $query = PeriodeEvaluation::with('anneeScolaire')
            ->withCount('notes');

        if ($request->filled('id_annee_scolaire')) {
            $query->parAnnee($request->id_annee_scolaire);
        }

        if ($request->filled('active')) {
            $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where('libelle', 'like', '%' . $request->search . '%');
        }

        $periodes = $query->orderBy('id_annee_scolaire')
            ->orderBy('ordre')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $periodes,
        ]);
    }

    /**
     * POST /api/periodes-evaluation
     */
    public function store(StorePeriodeEvaluationRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Vérifier que les dates sont dans l'année scolaire
        $annee = \App\Models\AnneeScolaire::find($data['id_annee_scolaire']);
        if (!$annee) {
            return response()->json([
                'success' => false,
                'message' => "Année scolaire introuvable.",
            ], 404);
        }

        if ($data['date_debut'] < $annee->date_debut->toDateString()
            || $data['date_fin'] > $annee->date_fin->toDateString()) {
            return response()->json([
                'success' => false,
                'message' => "Les dates de la période doivent être comprises dans l'année scolaire ({$annee->date_debut->format('d/m/Y')} → {$annee->date_fin->format('d/m/Y')}).",
            ], 422);
        }

        // Vérifier l'unicité de l'ordre dans l'année
        $ordreExiste = PeriodeEvaluation::where('id_annee_scolaire', $data['id_annee_scolaire'])
            ->where('ordre', $data['ordre'])
            ->exists();

        if ($ordreExiste) {
            return response()->json([
                'success' => false,
                'message' => "Une période avec l'ordre {$data['ordre']} existe déjà pour cette année scolaire.",
            ], 409);
        }

        // Détection de chevauchement avec d'autres périodes
        $chevauchement = PeriodeEvaluation::where('id_annee_scolaire', $data['id_annee_scolaire'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('date_debut', [$data['date_debut'], $data['date_fin']])
                  ->orWhereBetween('date_fin',   [$data['date_debut'], $data['date_fin']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('date_debut', '<=', $data['date_debut'])
                         ->where('date_fin',   '>=', $data['date_fin']);
                  });
            })
            ->exists();

        if ($chevauchement) {
            return response()->json([
                'success' => false,
                'message' => "Cette période chevauche une autre période de la même année scolaire.",
            ], 409);
        }

        DB::beginTransaction();
        try {
            $periode = PeriodeEvaluation::create($data);

            // Si la nouvelle période est active, désactiver les autres de la même année
            if (!empty($data['active'])) {
                PeriodeEvaluation::where('id_annee_scolaire', $data['id_annee_scolaire'])
                    ->where('id_periode', '!=', $periode->id_periode)
                    ->update(['active' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Période d'évaluation créée avec succès.",
                'data'    => $periode->load('anneeScolaire'),
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
     * GET /api/periodes-evaluation/{id}
     */
    public function show(int $id): JsonResponse
    {
        $periode = PeriodeEvaluation::with('anneeScolaire')
            ->withCount('notes')
            ->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => "Période d'évaluation introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $periode,
        ]);
    }

    /**
     * PUT/PATCH /api/periodes-evaluation/{id}
     */
    public function update(UpdatePeriodeEvaluationRequest $request, int $id): JsonResponse
    {
        $periode = PeriodeEvaluation::find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => "Période d'évaluation introuvable.",
            ], 404);
        }

        $data = $request->validated();

        // Fusionner pour validation date_debut < date_fin
        $dateDebut = $data['date_debut'] ?? $periode->date_debut->toDateString();
        $dateFin   = $data['date_fin']   ?? $periode->date_fin->toDateString();

        if ($dateFin <= $dateDebut) {
            return response()->json([
                'success' => false,
                'message' => "La date de fin doit être postérieure à la date de début.",
            ], 422);
        }

        // Vérifier l'unicité de l'ordre (si modifié)
        if (isset($data['ordre'])) {
            $ordreExiste = PeriodeEvaluation::where('id_annee_scolaire', $periode->id_annee_scolaire)
                ->where('ordre', $data['ordre'])
                ->where('id_periode', '!=', $id)
                ->exists();

            if ($ordreExiste) {
                return response()->json([
                    'success' => false,
                    'message' => "Une période avec l'ordre {$data['ordre']} existe déjà pour cette année scolaire.",
                ], 409);
            }
        }

        DB::beginTransaction();
        try {
            $periode->update($data);

            if (!empty($data['active'])) {
                PeriodeEvaluation::where('id_annee_scolaire', $periode->id_annee_scolaire)
                    ->where('id_periode', '!=', $periode->id_periode)
                    ->update(['active' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Période d'évaluation mise à jour.",
                'data'    => $periode->fresh()->load('anneeScolaire'),
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
     * DELETE /api/periodes-evaluation/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $periode = PeriodeEvaluation::withCount('notes')->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => "Période d'évaluation introuvable.",
            ], 404);
        }

        if ($periode->notes_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$periode->notes_count} note(s) sont rattachées à cette période.",
            ], 409);
        }

        $periode->delete();

        return response()->json([
            'success' => true,
            'message' => "Période d'évaluation supprimée.",
        ]);
    }

    /**
     * POST /api/periodes-evaluation/{id}/activer
     */
    public function activer(int $id): JsonResponse
    {
        $periode = PeriodeEvaluation::find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => "Période d'évaluation introuvable.",
            ], 404);
        }

        DB::beginTransaction();
        try {
            PeriodeEvaluation::where('id_annee_scolaire', $periode->id_annee_scolaire)
                ->update(['active' => false]);

            $periode->update(['active' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Période d'évaluation activée.",
                'data'    => $periode->fresh(),
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
     * GET /api/periodes-evaluation/active?id_annee_scolaire=1
     */
    public function active(Request $request): JsonResponse
    {
        $query = PeriodeEvaluation::active();

        if ($request->filled('id_annee_scolaire')) {
            $query->parAnnee($request->id_annee_scolaire);
        }

        $periode = $query->first();

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => "Aucune période active trouvée.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $periode,
        ]);
    }

    /**
     * GET /api/periodes-evaluation/annee/{idAnnee}
     * Toutes les périodes d'une année, triées par ordre
     */
    public function parAnnee(int $idAnnee): JsonResponse
    {
        $periodes = PeriodeEvaluation::withCount('notes')
            ->parAnnee($idAnnee)
            ->ordonne()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $periodes,
        ]);
    }
}