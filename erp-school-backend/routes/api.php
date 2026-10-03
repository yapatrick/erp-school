<?php

/**
 * ROUTES API - JWT & SANCTUM
 * 
 * Fichier: routes/api.php
 * 
 * Configuration:
 * - Routes publiques : authentification
 * - Routes protégées : par rôle
 * - Deux guards : api (JWT) et sanctum (Sanctum)
 * 
 * Utilisation dans React:
 * 
 * JWT:
 *   const token = localStorage.getItem('token');
 *   fetch(url, {
 *     headers: { 'Authorization': 'Bearer ' + token }
 *   })
 * 
 * Sanctum:
 *   fetch(url, {
 *     credentials: 'include'  // Envoie les cookies
 *   })
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ProfesseurController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\EnseignementController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TypePaiementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PeriodeEvaluationController;


// ==========================================
// ROUTES PUBLIQUES - AUTHENTIFICATION
// ==========================================

Route::prefix('auth')->group(function () {
    
    // JWT Authentication
    Route::post('login/jwt', [AuthController::class, 'loginJWT']);
    Route::post('register', [AuthController::class, 'register']);
    
    // Sanctum Authentication
    Route::post('login/sanctum', [AuthController::class, 'loginSanctum']);
    
    // CSRF Token (pour Sanctum)
    Route::post('csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });
});

// Routes publiques (Accessibles sans token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// ROUTES PROTÉGÉES - JWT
// ==========================================

Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::post('logout/jwt', [AuthController::class, 'logoutJWT']);
    Route::post('refresh/jwt', [AuthController::class, 'refresh']);
    Route::get('me/jwt', [AuthController::class, 'meJWT']);
});

// ==========================================
// ROUTES PROTÉGÉES - SANCTUM
// ==========================================

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('logout/sanctum', [AuthController::class, 'logoutSanctum']);
    Route::get('me/sanctum', [AuthController::class, 'meSanctum']);
});

// ==========================================
// API ROUTES - UTILISER UN DES DEUX GUARDS
// ==========================================

// Choix : utiliser 'auth:api' pour JWT ou 'auth:sanctum' pour Sanctum
// OU les deux : 'auth:api,sanctum' (accepte les deux)

Route::middleware(['auth:api,sanctum'])->prefix('v1')->group(function () {
    
    // ==========================================
    // ETUDIANTS - Accessible à tous les rôles
    // ==========================================
    Route::apiResource('etudiants', EtudiantController::class);
    Route::get('etudiants/classe/{classeId}', [EtudiantController::class, 'parClasse']);
    Route::get('etudiants/{id}/inscriptions', [EtudiantController::class, 'inscriptions']);
    Route::get('etudiants/{id}/paiements', [EtudiantController::class, 'paiements']);
    Route::get('etudiants/{id}/notes', [EtudiantController::class, 'notes']);
    Route::get('etudiants/{id}/absences', [EtudiantController::class, 'absences']);
    
    // ==========================================
    // INSCRIPTIONS - Admin, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Secrétaire')->group(function () {
        Route::apiResource('inscriptions', InscriptionController::class);
        Route::get('inscriptions/etudiant/{etudiantId}', [InscriptionController::class, 'parEtudiant']);
        Route::get('inscriptions/classe/{classeId}', [InscriptionController::class, 'parClasse']);
        Route::patch('inscriptions/{id}/valider', [InscriptionController::class, 'valider']);
        Route::patch('inscriptions/{id}/annuler', [InscriptionController::class, 'annuler']);
        Route::get('inscriptions/statut/{statut}', [InscriptionController::class, 'parStatut']);
    });
    
    // ==========================================
    // PAIEMENTS - Admin, Caissier, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Caissier,Secrétaire')->group(function () {
        Route::get('paiements', [PaiementController::class, 'index']);
        Route::post('paiements', [PaiementController::class, 'store']);
        Route::get('paiements/{id}', [PaiementController::class, 'show']);
        Route::put('paiements/{id}', [PaiementController::class, 'update']);
        
        // Routes spéciales - caisse
        Route::get('paiements/etudiant/{etudiantId}', [PaiementController::class, 'paiementsEtudiant']);
        Route::get('rapport-caisse', [PaiementController::class, 'rapportCaisse']);
        Route::get('soldes-impayés', [PaiementController::class, 'soldesImpayes']);
        Route::get('reçu/{numeroRecu}', [PaiementController::class, 'imprimeRecu']);
    });
    
    // ==========================================
    // NOTES - Professeur, Admin
    // ==========================================
    Route::middleware('role:Professeur,Admin')->group(function () {
        Route::apiResource('notes', NoteController::class, ['only' => ['store', 'update', 'destroy']]);
        Route::get('notes/etudiant/{etudiantId}', [NoteController::class, 'noteEtudiant']);
        Route::get('notes/etudiant/{etudiantId}/bulletin/{periodeId}', [NoteController::class, 'bulletinPeriode']);
        Route::get('notes/classe/{classeId}/periode/{periodeId}', [NoteController::class, 'bulletinClasse']);
        Route::get('notes/matiere/{matiereId}/periode/{periodeId}', [NoteController::class, 'moyennesMatiere']);
    });
    
    // GET notes - accessible à tous
    Route::get('notes', [NoteController::class, 'index']);
    Route::get('notes/{id}', [NoteController::class, 'show']);
    
    // ==========================================
    // ABSENCES - Professeur, Admin
    // ==========================================
    Route::middleware('role:Professeur,Admin')->group(function () {
        Route::post('absences', [AbsenceController::class, 'store']);
        Route::put('absences/{id}', [AbsenceController::class, 'update']);
        Route::patch('absences/{id}/justifier', [AbsenceController::class, 'justifier']);
        Route::delete('absences/{id}', [AbsenceController::class, 'destroy']);
    });
    
    // GET absences - accessible à tous
    Route::get('absences/etudiant/{etudiantId}', [AbsenceController::class, 'absencesEtudiant']);
    Route::get('absences/classe/{classeId}', [AbsenceController::class, 'absencesClasse']);
    
    // ==========================================
    // CLASSES - Admin, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Secrétaire')->group(function () {
        Route::apiResource('classes', ClasseController::class);
        Route::get('classes/{id}/etudiants', [ClasseController::class, 'etudiants']);
        Route::get('classes/{id}/emploi-temps', [ClasseController::class, 'emploiTemps']);
    });
    
    Route::get('classes', [ClasseController::class, 'index']);
    Route::get('classes/{id}', [ClasseController::class, 'show']);
    
    // ==========================================
    // MATIÈRES - Admin
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('matieres', MatiereController::class);
    });
    
    Route::get('matieres', [MatiereController::class, 'index']);
    Route::get('matieres/{id}', [MatiereController::class, 'show']);
    
    // ==========================================
    // PROFESSEURS - Admin, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Secrétaire')->group(function () {
        Route::apiResource('professeurs', ProfesseurController::class);
        Route::get('professeurs/{id}/enseignements', [ProfesseurController::class, 'enseignements']);
        Route::get('professeurs/{id}/notes', [ProfesseurController::class, 'notes']);
    });
    
    Route::get('professeurs', [ProfesseurController::class, 'index']);
    Route::get('professeurs/{id}', [ProfesseurController::class, 'show']);
    
    // ==========================================
    // ANNÉES SCOLAIRES - Admin
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('annees-scolaires', AnneeScolaireController::class);
        Route::patch('annees-scolaires/{id}/activer', [AnneeScolaireController::class, 'activer']);
        Route::get('annee-scolaire/actuelle', [AnneeScolaireController::class, 'actuelle']);
    });
    
    Route::get('annees-scolaires', [AnneeScolaireController::class, 'index']);
    
    // ==========================================
    // ENSEIGNEMENTS - Admin, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Secrétaire')->group(function () {
        Route::apiResource('enseignements', EnseignementController::class);
        Route::get('emploi-temps/classe/{classeId}', [EnseignementController::class, 'emploiTempsClasse']);
        Route::get('emploi-temps/professeur/{professeurId}', [EnseignementController::class, 'emploiTempsProfesseur']);
    });
    
    Route::get('enseignements', [EnseignementController::class, 'index']);
    Route::get('enseignements/{id}', [EnseignementController::class, 'show']);
    
    // ==========================================
    // DASHBOARD & STATISTIQUES - Tous
    // ==========================================
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/statistiques', [DashboardController::class, 'statistiques']);
    Route::get('dashboard/etudiants-actifs', [DashboardController::class, 'etudiantsActifs']);
    Route::get('dashboard/revenus', [DashboardController::class, 'revenus']);
    
    // ==========================================
    // RAPPORTS - Admin, Secrétaire
    // ==========================================
    Route::middleware('role:Admin,Secrétaire')->group(function () {
        Route::get('rapports/inscriptions', [DashboardController::class, 'rapportInscriptions']);
        Route::get('rapports/paiements', [DashboardController::class, 'rapportPaiements']);
        Route::get('rapports/notes', [DashboardController::class, 'rapportNotes']);
        Route::get('rapports/absences', [DashboardController::class, 'rapportAbsences']);
    });


    Route::prefix('permissions')->group(function () {
    Route::get('/',               [PermissionController::class, 'index']);
    Route::get('/categories',     [PermissionController::class, 'categories']); // AVANT /{id}
    Route::post('/',              [PermissionController::class, 'store']);

    Route::get('/{id}',           [PermissionController::class, 'show']);
    Route::put('/{id}',           [PermissionController::class, 'update']);
    Route::patch('/{id}',         [PermissionController::class, 'update']);
    Route::delete('/{id}',        [PermissionController::class, 'destroy']);

    Route::post('/{id}/toggle',   [PermissionController::class, 'toggle']);
});


Route::prefix('classes')->group(function () {
    Route::get('/',                    [ClasseController::class, 'index']);
    Route::post('/',                   [ClasseController::class, 'store']);

    // Routes spécifiques AVANT /{id}
    Route::get('/{id}/etudiants',      [ClasseController::class, 'etudiants']);
    Route::get('/{id}/statistiques',   [ClasseController::class, 'statistiques']);
    Route::post('/{id}/toggle',        [ClasseController::class, 'toggle']);

    Route::get('/{id}',                [ClasseController::class, 'show']);
    Route::put('/{id}',                [ClasseController::class, 'update']);
    Route::patch('/{id}',              [ClasseController::class, 'update']);
    Route::delete('/{id}',             [ClasseController::class, 'destroy']);
});


Route::prefix('types-paiement')->group(function () {
    Route::get('/',              [TypePaiementController::class, 'index']);
    Route::post('/',             [TypePaiementController::class, 'store']);

    Route::get('/{id}',          [TypePaiementController::class, 'show']);
    Route::put('/{id}',          [TypePaiementController::class, 'update']);
    Route::patch('/{id}',        [TypePaiementController::class, 'update']);
    Route::delete('/{id}',       [TypePaiementController::class, 'destroy']);

    Route::post('/{id}/toggle',  [TypePaiementController::class, 'toggle']);
});


});

// ==========================================
// FALLBACK - Route non trouvée
// ==========================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route non trouvée'
    ], 404);
});

/**
 * ==========================================
 * EXEMPLE D'UTILISATION DEPUIS REACT
 * ==========================================
 * 
 * 1. AVEC JWT:
 * 
 *    // Connexion
 *    const response = await fetch('api/auth/login/jwt', {
 *      method: 'POST',
 *      headers: { 'Content-Type': 'application/json' },
 *      body: JSON.stringify({ login: 'admin', mot_de_passe: 'password' })
 *    });
 *    const { token } = await response.json();
 *    localStorage.setItem('token', token);
 *    
 *    // Requête authentifiée
 *    const notes = await fetch('api/v1/notes', {
 *      headers: {
 *        'Authorization': 'Bearer ' + localStorage.getItem('token'),
 *        'Content-Type': 'application/json'
 *      }
 *    }).then(r => r.json());
 * 
 * 2. AVEC SANCTUM:
 * 
 *    // Connexion
 *    const response = await fetch('api/auth/login/sanctum', {
 *      method: 'POST',
 *      headers: { 'Content-Type': 'application/json' },
 *      credentials: 'include',
 *      body: JSON.stringify({ login: 'admin', mot_de_passe: 'password' })
 *    });
 *    const { token } = await response.json();
 *    
 *    // Requête authentifiée (les cookies sont envoyés automatiquement)
 *    const notes = await fetch('api/v1/notes', {
 *      credentials: 'include',
 *      headers: { 'Content-Type': 'application/json' }
 *    }).then(r => r.json());
 * 
 * 3. CROCHETS REACT RÉUTILISABLES:
 * 
 *    // useApi.js
 *    const useApi = (method = 'GET') => {
 *      const [loading, setLoading] = useState(false);
 *      const [error, setError] = useState(null);
 *      
 *      const request = async (url, body = null) => {
 *        setLoading(true);
 *        try {
 *          const options = {
 *            method,
 *            headers: { 'Content-Type': 'application/json' },
 *            credentials: 'include' // Sanctum
 *          };
 *          
 *          // JWT: ajouter le token
 *          const token = localStorage.getItem('token');
 *          if (token) {
 *            options.headers['Authorization'] = 'Bearer ' + token;
 *          }
 *          
 *          if (body) options.body = JSON.stringify(body);
 *          
 *          const response = await fetch(url, options);
 *          if (!response.ok) throw new Error('API Error');
 *          return await response.json();
 *        } catch (err) {
 *          setError(err.message);
 *          return null;
 *        } finally {
 *          setLoading(false);
 *        }
 *      };
 *      
 *      return { request, loading, error };
 *    };
 *    
 *    // Utilisation
 *    const { request } = useApi('GET');
 *    const notes = await request('/api/v1/notes');
 */