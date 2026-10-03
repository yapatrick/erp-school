<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfesseurRequest;
use App\Http\Requests\UpdateProfesseurRequest;
use App\Models\Professeur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfesseurController extends Controller
{
    /**
     * GET /api/professeurs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Professeur::query()
            ->withCount(['enseignements', 'notes', 'absences']);

        if ($request->filled('actif')) {
            $query->where('actif', filter_var($request->actif, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('specialite')) {
            $query->parSpecialite($request->specialite);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $professeurs = $query->orderBy('nom')->orderBy('prenom')->get();

        return response()->json([
            'success' => true,
            'data'    => $professeurs,
        ]);
    }

    /**
     * POST /api/professeurs
     */
    public function store(StoreProfesseurRequest $request): JsonResponse
    {
        $professeur = Professeur::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Professeur créé avec succès.",
            'data'    => $professeur,
        ], 201);
    }

    /**
     * GET /api/professeurs/{id}
     */
    public function show(int $id): JsonResponse
    {
        $professeur = Professeur::with([
            'enseignements.classe',
            'enseignements.matiere',
        ])
        ->withCount(['enseignements', 'notes', 'absences'])
        ->find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $professeur,
        ]);
    }

    /**
     * PUT/PATCH /api/professeurs/{id}
     */
    public function update(UpdateProfesseurRequest $request, int $id): JsonResponse
    {
        $professeur = Professeur::find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        $professeur->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Professeur mis à jour.",
            'data'    => $professeur->fresh(),
        ]);
    }

    /**
     * DELETE /api/professeurs/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $professeur = Professeur::withCount(['enseignements', 'notes', 'absences'])->find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        if ($professeur->enseignements_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$professeur->enseignements_count} enseignement(s) rattaché(s). Désactivez plutôt ce professeur.",
            ], 409);
        }

        if ($professeur->notes_count > 0 || $professeur->absences_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : ce professeur a des notes ou absences enregistrées.",
            ], 409);
        }

        $professeur->delete();

        return response()->json([
            'success' => true,
            'message' => "Professeur supprimé.",
        ]);
    }

    /**
     * POST /api/professeurs/{id}/toggle
     */
    public function toggle(int $id): JsonResponse
    {
        $professeur = Professeur::find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        $professeur->update(['actif' => !$professeur->actif]);

        return response()->json([
            'success' => true,
            'message' => $professeur->actif
                ? "Professeur activé."
                : "Professeur désactivé.",
            'data'    => $professeur->fresh(),
        ]);
    }

    /**
     * GET /api/professeurs/{id}/planning
     * Planning hebdomadaire du professeur (regroupé par jour)
     */
    public function planning(int $id): JsonResponse
    {
        $professeur = Professeur::find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        $enseignements = $professeur->enseignements()
            ->with(['classe', 'matiere'])
            ->orderByRaw("FIELD(jour_semaine, 'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        $parJour = $enseignements->groupBy('jour_semaine');

        return response()->json([
            'success' => true,
            'data'    => [
                'professeur' => $professeur,
                'planning'   => $parJour,
                'total_heures_semaine' => $enseignements->sum('heures_semaine'),
            ],
        ]);
    }

    /**
     * GET /api/professeurs/{id}/statistiques
     */
    public function statistiques(int $id): JsonResponse
    {
        $professeur = Professeur::find($id);

        if (!$professeur) {
            return response()->json([
                'success' => false,
                'message' => "Professeur introuvable.",
            ], 404);
        }

        $totalHeures = $professeur->enseignements()->sum('heures_semaine');
        $nbClasses   = $professeur->enseignements()->distinct('id_classe')->count('id_classe');
        $nbMatieres  = $professeur->enseignements()->distinct('id_matiere')->count('id_matiere');

        return response()->json([
            'success' => true,
            'data' => [
                'total_heures_semaine' => (int) $totalHeures,
                'nb_classes'           => $nbClasses,
                'nb_matieres'          => $nbMatieres,
                'nb_enseignements'     => $professeur->enseignements()->count(),
                'nb_notes'             => $professeur->notes()->count(),
                'nb_absences'          => $professeur->absences()->count(),
                'date_embauche'        => $professeur->date_embauche,
                'salaire'              => $professeur->salaire,
            ],
        ]);
    }
}