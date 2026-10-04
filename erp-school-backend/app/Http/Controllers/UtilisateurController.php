<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UtilisateurController extends Controller
{
    /**
     * Liste des employés (profils Utilisateur).
     * GET /api/utilisateurs
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Utilisateur::class);

        $query = Utilisateur::with('user.roles');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('poste')) {
            $query->where('poste', $request->input('poste'));
        }

        if ($request->filled('role')) {
            $query->whereHas('user.roles', fn ($q) => $q->where('slug', $request->input('role')));
        }

        if ($request->filled('actif')) {
            $query->whereHas('user', fn ($q) => $q->where('actif', $request->boolean('actif')));
        }

        $perPage = (int) $request->input('per_page', 15);
        $employes = $query->orderBy('date_embauche', 'desc')->paginate($perPage);

        return response()->json($employes, 200);
    }

    /**
     * Créer un profil employé (Utilisateur) lié à un User existant.
     * POST /api/utilisateurs
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Utilisateur::class);

        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|integer|exists:users,id|unique:utilisateurs,user_id',
            'matricule'     => 'required|string|max:50|unique:utilisateurs,matricule',
            'poste'         => 'required|string|max:100',
            'telephone'     => 'nullable|string|max:20',
            'adresse'       => 'nullable|string|max:255',
            'date_embauche' => 'required|date|before_or_equal:today',
            'salaire'       => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $utilisateur = Utilisateur::create($validator->validated());

        return response()->json([
            'message'     => 'Profil employé créé avec succès.',
            'utilisateur' => $utilisateur->load('user.roles'),
        ], 201);
    }

    /**
     * Afficher un profil employé.
     * GET /api/utilisateurs/{id}
     */
    public function show(string $id)
    {
        $utilisateur = Utilisateur::with('user.roles.permissions')->find($id);

        if (!$utilisateur) {
            return response()->json(['message' => 'Profil employé non trouvé.'], 404);
        }

        Gate::authorize('view', $utilisateur);

        return response()->json(['utilisateur' => $utilisateur], 200);
    }

    /**
     * Mettre à jour un profil employé.
     * PUT /api/utilisateurs/{id}
     */
    public function update(Request $request, string $id)
    {
        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json(['message' => 'Profil employé non trouvé.'], 404);
        }

        Gate::authorize('update', $utilisateur);

        $validator = Validator::make($request->all(), [
            'user_id'       => [
                'sometimes', 'integer', 'exists:users,id',
                Rule::unique('utilisateurs', 'user_id')->ignore($utilisateur->id_utilisateur, 'id_utilisateur'),
            ],
            'matricule'     => [
                'sometimes', 'string', 'max:50',
                Rule::unique('utilisateurs', 'matricule')->ignore($utilisateur->id_utilisateur, 'id_utilisateur'),
            ],
            'poste'         => 'sometimes|string|max:100',
            'telephone'     => 'nullable|string|max:20',
            'adresse'       => 'nullable|string|max:255',
            'date_embauche' => 'sometimes|date|before_or_equal:today',
            'salaire'       => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $utilisateur->update($validator->validated());

        return response()->json([
            'message'     => 'Profil employé mis à jour avec succès.',
            'utilisateur' => $utilisateur->fresh('user.roles'),
        ], 200);
    }

    /**
     * Supprimer un profil employé.
     * DELETE /api/utilisateurs/{id}
     */
    public function destroy(string $id)
    {
        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json(['message' => 'Profil employé non trouvé.'], 404);
        }

        Gate::authorize('delete', $utilisateur);

        $utilisateur->delete();

        return response()->json(['message' => 'Profil employé supprimé avec succès.'], 200);
    }

    // ==========================================
    // MÉTHODES SPÉCIALES
    // ==========================================

    /**
     * Créer en une seule requête un User + son profil Utilisateur.
     * POST /api/utilisateurs/with-user
     *
     * Très pratique pour l'inscription d'un employé par un Admin.
     */
    public function storeWithUser(Request $request)
    {
        Gate::authorize('create', Utilisateur::class);

        $validator = Validator::make($request->all(), [
            // Champs du User
            'name'                  => 'required|string|max:50|unique:users,name',
            'email'                 => 'required|email|max:100|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'actif'                 => 'sometimes|boolean',
            'roles'                 => 'sometimes|array',
            'roles.*'               => 'string|exists:roles,slug',

            // Champs du profil Utilisateur
            'matricule'             => 'required|string|max:50|unique:utilisateurs,matricule',
            'poste'                 => 'required|string|max:100',
            'telephone'             => 'nullable|string|max:20',
            'adresse'               => 'nullable|string|max:255',
            'date_embauche'         => 'required|date|before_or_equal:today',
            'salaire'               => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Transaction : soit tout réussit, soit tout échoue
        $result = DB::transaction(function () use ($data) {
            // 1. Créer le User (auth)
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'actif'    => $data['actif'] ?? true,
            ]);

            // 2. Assigner les rôles
            if (!empty($data['roles'])) {
                $roleIds = \App\Models\Role::whereIn('slug', $data['roles'])->pluck('id_role');
                $user->roles()->sync($roleIds);
            }

            // 3. Créer le profil Utilisateur
            $utilisateur = Utilisateur::create([
                'user_id'       => $user->id,
                'matricule'     => $data['matricule'],
                'poste'         => $data['poste'],
                'telephone'     => $data['telephone'] ?? null,
                'adresse'       => $data['adresse'] ?? null,
                'date_embauche' => $data['date_embauche'],
                'salaire'       => $data['salaire'] ?? null,
            ]);

            return [$user, $utilisateur];
        });

        [$user, $utilisateur] = $result;

        return response()->json([
            'message'     => 'Employé créé avec succès (User + Profil).',
            'user'        => $user->load('roles.permissions'),
            'utilisateur' => $utilisateur,
        ], 201);
    }

    /**
     * Récupérer le profil employé à partir de l'utilisateur connecté.
     * GET /api/utilisateurs/me
     */
    public function me(Request $request)
    {
        $utilisateur = Utilisateur::with('user.roles.permissions')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$utilisateur) {
            return response()->json([
                'message' => "Aucun profil employé associé à votre compte."
            ], 404);
        }

        return response()->json(['utilisateur' => $utilisateur], 200);
    }

    /**
     * Statistiques simples sur les employés.
     * GET /api/utilisateurs/stats
     */
    public function stats()
    {
        Gate::authorize('viewAny', Utilisateur::class);

        return response()->json([
            'total_employes'      => Utilisateur::count(),
            'par_poste'           => Utilisateur::select('poste', DB::raw('count(*) as total'))
                                        ->groupBy('poste')
                                        ->get(),
            'salaire_moyen'       => round(Utilisateur::avg('salaire') ?? 0, 2),
            'embauches_ce_mois'   => Utilisateur::whereMonth('date_embauche', now()->month)
                                        ->whereYear('date_embauche', now()->year)
                                        ->count(),
        ], 200);
    }
}