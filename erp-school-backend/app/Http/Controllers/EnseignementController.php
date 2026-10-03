<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnseignementRequest;
use App\Http\Requests\UpdateEnseignementRequest;
use App\Models\Enseignement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnseignementController extends Controller
{
    /**
     * GET /api/enseignements
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enseignement::with(['classe', 'matiere', 'professeur']);

        if ($request->filled('id_classe')) {
            $query->parClasse($request->id_classe);
        }

        if ($request->filled('id_professeur')) {
            $query->parProfesseur($request->id_professeur);
        }

        if ($request->filled('jour')) {
            $query->parJour($request->jour);
        }

        if ($request->filled('id_matiere')) {
            $query->where('id_matiere', $request->id_matiere);
        }

        $enseignements = $query
            ->orderByRaw("FIELD(jour_semaine, 'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $enseignements,
        ]);
    }

    /**
     * POST /api/enseignements
     */
    public function store(StoreEnseignementRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Détection de conflit d'horaire pour la même classe
        $conflitClasse = Enseignement::where('id_classe', $data['id_classe'])
            ->where('jour_semaine', $data['jour_semaine'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('heure_debut', [$data['heure_debut'], $data['heure_fin']])
                  ->orWhereBetween('heure_fin',   [$data['heure_debut'], $data['heure_fin']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('heure_debut', '<=', $data['heure_debut'])
                         ->where('heure_fin',   '>=', $data['heure_fin']);
                  });
            })
            ->exists();

        if ($conflitClasse) {
            return response()->json([
                'success' => false,
                'message' => "Conflit d'horaire : cette classe a déjà un cours sur ce créneau.",
            ], 409);
        }

        // Détection de conflit pour le professeur
        $conflitProf = Enseignement::where('id_professeur', $data['id_professeur'])
            ->where('jour_semaine', $data['jour_semaine'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('heure_debut', [$data['heure_debut'], $data['heure_fin']])
                  ->orWhereBetween('heure_fin',   [$data['heure_debut'], $data['heure_fin']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('heure_debut', '<=', $data['heure_debut'])
                         ->where('heure_fin',   '>=', $data['heure_fin']);
                  });
            })
            ->exists();

        if ($conflitProf) {
            return response()->json([
                'success' => false,
                'message' => "Conflit d'horaire : ce professeur est déjà occupé sur ce créneau.",
            ], 409);
        }

        $enseignement = Enseignement::create($data);

        return response()->json([
            'success' => true,
            'message' => "Enseignement créé avec succès.",
            'data'    => $enseignement->load(['classe', 'matiere', 'professeur']),
        ], 201);
    }

    /**
     * GET /api/enseignements/{id}
     */
    public function show(int $id): JsonResponse
    {
        $enseignement = Enseignement::with(['classe', 'matiere', 'professeur', 'absences'])
            ->find($id);

        if (!$enseignement) {
            return response()->json([
                'success' => false,
                'message' => "Enseignement introuvable.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $enseignement,
        ]);
    }

    /**
     * PUT/PATCH /api/enseignements/{id}
     */
    public function update(UpdateEnseignementRequest $request, int $id): JsonResponse
    {
        $enseignement = Enseignement::find($id);

        if (!$enseignement) {
            return response()->json([
                'success' => false,
                'message' => "Enseignement introuvable.",
            ], 404);
        }

        $data = $request->validated();

        // Fusionner avec les valeurs existantes pour la détection de conflit
        $jour        = $data['jour_semaine']  ?? $enseignement->jour_semaine;
        $heureDebut  = $data['heure_debut']   ?? $enseignement->heure_debut->format('H:i');
        $heureFin    = $data['heure_fin']     ?? $enseignement->heure_fin->format('H:i');
        $idClasse    = $data['id_classe']     ?? $enseignement->id_classe;
        $idProf      = $data['id_professeur'] ?? $enseignement->id_professeur;

        $conflit = Enseignement::where('id_enseignement', '!=', $id)
            ->where('jour_semaine', $jour)
            ->where(function ($q) use ($idClasse, $idProf) {
                $q->where('id_classe', $idClasse)
                  ->orWhere('id_professeur', $idProf);
            })
            ->where(function ($q) use ($heureDebut, $heureFin) {
                $q->whereBetween('heure_debut', [$heureDebut, $heureFin])
                  ->orWhereBetween('heure_fin',   [$heureDebut, $heureFin])
                  ->orWhere(function ($q2) use ($heureDebut, $heureFin) {
                      $q2->where('heure_debut', '<=', $heureDebut)
                         ->where('heure_fin',   '>=', $heureFin);
                  });
            })
            ->exists();

        if ($conflit) {
            return response()->json([
                'success' => false,
                'message' => "Conflit d'horaire détecté pour cette classe ou ce professeur.",
            ], 409);
        }

        $enseignement->update($data);

        return response()->json([
            'success' => true,
            'message' => "Enseignement mis à jour.",
            'data'    => $enseignement->fresh()->load(['classe', 'matiere', 'professeur']),
        ]);
    }

    /**
     * DELETE /api/enseignements/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $enseignement = Enseignement::find($id);

        if (!$enseignement) {
            return response()->json([
                'success' => false,
                'message' => "Enseignement introuvable.",
            ], 404);
        }

        // Empêcher la suppression s'il y a des absences liées
        if ($enseignement->absences()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : des absences sont liées à cet enseignement.",
            ], 409);
        }

        $enseignement->delete();

        return response()->json([
            'success' => true,
            'message' => "Enseignement supprimé.",
        ]);
    }

    /**
     * GET /api/enseignements/emploi-du-temps/{idClasse}
     * Emploi du temps d'une classe (groupé par jour)
     */
    public function emploiDuTemps(int $idClasse): JsonResponse
    {
        $enseignements = Enseignement::with(['matiere', 'professeur'])
            ->parClasse($idClasse)
            ->orderByRaw("FIELD(jour_semaine, 'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        $parJour = $enseignements->groupBy('jour_semaine');

        return response()->json([
            'success' => true,
            'data'    => $parJour,
        ]);
    }

    /**
     * GET /api/enseignements/professeur/{idProfesseur}
     * Planning d'un professeur
     */
    public function planningProfesseur(int $idProfesseur): JsonResponse
    {
        $enseignements = Enseignement::with(['classe', 'matiere'])
            ->parProfesseur($idProfesseur)
            ->orderByRaw("FIELD(jour_semaine, 'lundi','mardi','mercredi','jeudi','vendredi','samedi')")
            ->orderBy('heure_debut')
            ->get();

        $parJour = $enseignements->groupBy('jour_semaine');

        return response()->json([
            'success' => true,
            'data'    => $parJour,
        ]);
    }
}