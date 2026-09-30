<?php

namespace App\Http\Controllers;

// InscriptionController
// ==========================================
namespace App\Http\Controllers\Api;

use App\Models\Inscription;
use App\Models\Etudiant;
use App\Models\Classe;

class InscriptionController extends Controller
{
    /**
     * GET - Lister les inscriptions
     */
    public function index(Request $request)
    {
        $query = Inscription::with(['etudiant', 'classe', 'anneeScolaire']);

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('type_inscription')) {
            $query->where('type_inscription', $request->type_inscription);
        }

        if ($request->has('etudiant_id')) {
            $query->where('id_etudiant', $request->etudiant_id);
        }

        if ($request->has('classe_id')) {
            $query->where('id_classe', $request->classe_id);
        }

        // Inscriptions avec solde
        if ($request->boolean('avec_solde')) {
            $query->avecSolde();
        }

        $inscriptions = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inscriptions->items(),
            'pagination' => [
                'total' => $inscriptions->total(),
                'per_page' => $inscriptions->perPage(),
                'current_page' => $inscriptions->currentPage(),
                'last_page' => $inscriptions->lastPage()
            ]
        ]);
    }

    /**
     * POST - Créer une nouvelle inscription
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_etudiant' => 'required|exists:etudiants,id_etudiant',
            'id_classe' => 'required|exists:classes,id_classe',
            'id_annee_scolaire' => 'required|exists:annees_scolaires,id_annee_scolaire',
            'type_inscription' => 'required|in:Inscription,Réinscription',
            'montant_total' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $inscription = Inscription::create(array_merge(
            $request->all(),
            ['statut' => 'En cours', 'montant_paye' => 0]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Inscription créée',
            'data' => $inscription->load(['etudiant', 'classe'])
        ], 201);
    }

    /**
     * PUT - Mettre à jour une inscription
     */
    public function update(Request $request, $id)
    {
        $inscription = Inscription::find($id);

        if (!$inscription) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'statut' => 'sometimes|in:En cours,Validée,Annulée,Terminée',
            'montant_total' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $inscription->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inscription mise à jour',
            'data' => $inscription
        ]);
    }
}

