<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// ==========================================
// EtudiantController
// ==========================================
class EtudiantController extends Controller
{
    /**
     * GET - Lister tous les étudiants
     * Paramètres: page, per_page, search, actif, classe_id
     */
    public function index(Request $request)
    {
        $query = Etudiant::query();

        // Recherche par nom ou matricule
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('matricule', 'LIKE', "%{$search}%");
            });
        }

        // Filtrer par classe
        if ($request->has('classe_id')) {
            $query->parClasse($request->classe_id);
        }

        // Filtrer par statut actif
        if ($request->has('actif')) {
            $query->where('actif', $request->boolean('actif'));
        }

        $perPage = $request->get('per_page', 15);
        $etudiants = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Étudiants récupérés',
            'data' => $etudiants->items(),
            'pagination' => [
                'total' => $etudiants->total(),
                'per_page' => $etudiants->perPage(),
                'current_page' => $etudiants->currentPage(),
                'last_page' => $etudiants->lastPage(),
                'from' => $etudiants->firstItem(),
                'to' => $etudiants->lastItem()
            ]
        ]);
    }

    /**
     * POST - Créer un nouvel étudiant
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|unique:etudiants,matricule',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:500',
            'nom_parent' => 'nullable|string|max:200',
            'telephone_parent' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $etudiant = Etudiant::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Étudiant créé avec succès',
            'data' => $etudiant
        ], 201);
    }

    /**
     * GET - Afficher un étudiant spécifique
     */
    public function show($id)
    {
        $etudiant = Etudiant::with([
            'inscriptions' => function($q) {
                $q->latest('created_at')->limit(5);
            },
            'paiements' => function($q) {
                $q->latest('created_at')->limit(10);
            },
            'notes' => function($q) {
                $q->latest('created_at')->limit(10);
            }
        ])->find($id);

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $etudiant
        ]);
    }

    /**
     * PUT - Mettre à jour un étudiant
     */
    public function update(Request $request, $id)
    {
        $etudiant = Etudiant::find($id);

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'matricule' => 'sometimes|string|unique:etudiants,matricule,' . $id . ',id_etudiant',
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'date_naissance' => 'sometimes|date|before:today',
            'sexe' => 'sometimes|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'adresse' => 'nullable|string|max:500',
            'nom_parent' => 'nullable|string|max:200',
            'telephone_parent' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $etudiant->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Étudiant mis à jour',
            'data' => $etudiant
        ]);
    }

    /**
     * DELETE - Supprimer un étudiant
     */
    public function destroy($id)
    {
        $etudiant = Etudiant::find($id);

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
        }

        // Soft delete (marquer comme inactif)
        $etudiant->update(['actif' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant supprimé'
        ]);
    }
}

