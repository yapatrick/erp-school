<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClasseRequest;
use App\Http\Requests\UpdateClasseRequest;
use App\Models\Classe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasseController extends Controller
{
    /**
     * GET /api/classes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Classe::with('anneeScolaire')
            ->withCount(['inscriptions', 'enseignements']);

        if ($request->filled('id_annee_scolaire')) {
            $query->parAnnee($request->id_annee_scolaire);
        }

        if ($request->filled('niveau')) {
            $query->parNiveau($request->niveau);
        }

        if ($request->filled('active')) {
            $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where('nom_classe', 'like', '%' . $request->search . '%');
        }

        $classes = $query->orderBy('niveau')->orderBy('nom_classe')->get();

        // Ajoute le nombre d'étudiants réellement inscrits (statut = Validée)
        $classes->each(function ($classe) {
            $classe->etudiants_count = $classe->etudiants()->count();
        });

        return response()->json([
            'success' => true,
            'data'    => $classes,
        ]);
    }

    /**
     * POST /api/classes
     */
    public function store(StoreClasseRequest $request): JsonResponse
    {
        $data = $request->validated();

        $classe = Classe::create([
            'nom_classe'        => $data['nom_classe'],
            'niveau'            => $data['niveau'],
            'capacite_max'      => $data['capacite_max'],
            'frais_inscription' => $data['frais_inscription'],
            'frais_scolarite'   => $data['frais_scolarite'],
            'id_annee_scolaire' => $data['id_annee_scolaire'],
            'active'            => $data['active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Classe créée avec succès.",
            'data'    => $classe->load('anneeScolaire'),
        ], 201);
    }

    /**
     * GET /api/classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $classe = Classe::with(['anneeScolaire', 'enseignements.matiere', 'enseignements.professeur'])
            ->withCount(['inscriptions', 'enseignements'])
            ->find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        $classe->etudiants_count = $classe->etudiants()->count();
        $classe->places_disponibles = max(0, $classe->capacite_max - $classe->etudiants_count);

        return response()->json([
            'success' => true,
            'data'    => $classe,
        ]);
    }

    /**
     * PUT/PATCH /api/classes/{id}
     */
    public function update(UpdateClasseRequest $request, int $id): JsonResponse
    {
        $classe = Classe::find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        $data = $request->validated();

        // Vérifier qu'on ne réduit pas la capacité en dessous du nombre d'inscrits
        if (isset($data['capacite_max'])) {
            $inscrits = $classe->inscriptions()->where('statut', 'Validée')->count();
            if ($data['capacite_max'] < $inscrits) {
                return response()->json([
                    'success' => false,
                    'message' => "La capacité maximale ({$data['capacite_max']}) ne peut pas être inférieure au nombre d'étudiants déjà inscrits ({$inscrits}).",
                ], 409);
            }
        }

        $classe->update($data);

        return response()->json([
            'success' => true,
            'message' => "Classe mise à jour.",
            'data'    => $classe->fresh()->load('anneeScolaire'),
        ]);
    }

    /**
     * DELETE /api/classes/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $classe = Classe::withCount(['inscriptions', 'enseignements'])->find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        if ($classe->inscriptions_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$classe->inscriptions_count} inscription(s) liée(s) à cette classe.",
            ], 409);
        }

        if ($classe->enseignements_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$classe->enseignements_count} enseignement(s) lié(s) à cette classe.",
            ], 409);
        }

        $classe->delete();

        return response()->json([
            'success' => true,
            'message' => "Classe supprimée.",
        ]);
    }

    /**
     * GET /api/classes/{id}/etudiants
     * Liste des étudiants inscrits (statut Validée)
     */
    public function etudiants(int $id): JsonResponse
    {
        $classe = Classe::find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        $etudiants = $classe->etudiants()->get();

        return response()->json([
            'success' => true,
            'data'    => $etudiants,
        ]);
    }

    /**
     * GET /api/classes/{id}/statistiques
     * Renvoie les stats : capacité, inscrits, places dispo, taux de remplissage
     */
    public function statistiques(int $id): JsonResponse
    {
        $classe = Classe::find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        $inscrits = $classe->inscriptions()->where('statut', 'Validée')->count();
        $placesDisponibles = max(0, $classe->capacite_max - $inscrits);
        $tauxRemplissage = $classe->capacite_max > 0
            ? round(($inscrits / $classe->capacite_max) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'capacite_max'        => $classe->capacite_max,
                'inscrits'            => $inscrits,
                'places_disponibles'  => $placesDisponibles,
                'taux_remplissage'    => $tauxRemplissage . ' %',
                'frais_inscription'   => $classe->frais_inscription,
                'frais_scolarite'     => $classe->frais_scolarite,
            ],
        ]);
    }

    /**
     * POST /api/classes/{id}/toggle
     */
    public function toggle(int $id): JsonResponse
    {
        $classe = Classe::find($id);

        if (!$classe) {
            return response()->json([
                'success' => false,
                'message' => "Classe introuvable.",
            ], 404);
        }

        $classe->update(['active' => !$classe->active]);

        return response()->json([
            'success' => true,
            'message' => $classe->active ? "Classe activée." : "Classe désactivée.",
            'data'    => $classe->fresh(),
        ]);
    }
}