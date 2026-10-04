<?php

/**
 * ROUTES API - JWT
 * 
 * Fichier: routes/api.php
 * 
 * Configuration:
 * - Routes publiques : authentification
 * - Routes protégées : par rôle
 * - Deux guards : api (JWT) et sanctum (Sanctum)
 * 
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
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\RoleController as ApiRoleController;
use App\Http\Controllers\PermissionController as ApiPermissionController;



// ==========================================
// ROUTES PUBLIQUES - AUTHENTIFICATION
// ==========================================

// Routes publiques (Accessibles sans token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// ROUTES PROTÉGÉES - JWT
// ==========================================

Route::middleware('auth:api')->group(function () {
     Route::post('auth/logout/jwt', [AuthController::class, 'logoutJWT']);
    Route::post('auth/refresh/jwt', [AuthController::class, 'refresh']);
    Route::get('auth/me/jwt', [AuthController::class, 'meJWT']);
});

// ==========================================
// API ROUTES - UTILISER UN DES DEUX GUARDS
// ==========================================

Route::middleware(['auth:api'])->prefix('v1')->group(function () {

    Route::middleware(['auth:api', 'scope:Admin'])->group(function () {
        Route::get('/',              [UserController::class, 'index']);       // GET    /api/users
        Route::post('/',             [UserController::class, 'store']);       // POST   /api/users
        Route::get('/{id}',          [UserController::class, 'show']);        // GET    /api/users/{id}
        Route::put('/{id}',          [UserController::class, 'update']);      // PUT    /api/users/{id}
        Route::delete('/{id}',       [UserController::class, 'destroy']);     // DELETE /api/users/{id}
        
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
    });

    Route::middleware(['auth:api', 'scope:Caissier,Secretaire,Manager'])->group(function () {
        Route::apiResource('paiements', PaiementController::class);

        // Routes spéciales - caisse
        Route::get('paiements/etudiant/{etudiantId}', [PaiementController::class, 'paiementsEtudiant']);
        Route::get('rapport-caisse', [PaiementController::class, 'rapportCaisse']);
        Route::get('soldes-impayés', [PaiementController::class, 'soldesImpayes']);
        Route::get('reçu/{numeroRecu}', [PaiementController::class, 'imprimeRecu']);
    });

    //==========================================
    // Utilisateurs - Admin, Manager, Secretaire
    //==========================================
    Route::middleware(['auth:api', 'scope:Admin,Manager,Secretaire'])->group(function () {
        
        // Routes spéciales EN PREMIER (pour éviter les conflits avec {id})
        Route::get('/me',         [UtilisateurController::class, 'me']);
        Route::get('/stats',      [UtilisateurController::class, 'stats']);
        Route::post('/with-user', [UtilisateurController::class, 'storeWithUser']);
        Route::apiResource('utilisateurs', UtilisateurController::class);
    });

    // ==========================================
    // NOTES - Professeur, Manager
    // ==========================================
    Route::middleware(['auth:api', 'scope:Professeur,Manager'])->group(function () {
        Route::apiResource('notes', NoteController::class);

        Route::get('notes/etudiant/{etudiantId}', [NoteController::class, 'noteEtudiant']);
        Route::get('notes/etudiant/{etudiantId}/bulletin/{periodeId}', [NoteController::class, 'bulletinPeriode']);
        Route::get('notes/classe/{classeId}/periode/{periodeId}', [NoteController::class, 'bulletinClasse']);
        Route::get('notes/matiere/{matiereId}/periode/{periodeId}', [NoteController::class, 'moyennesMatiere']);
    });

    // GET notes - accessible à tous
    Route::get('notes', [NoteController::class, 'index']);
    Route::get('notes/{id}', [NoteController::class, 'show']);
    

     // ==========================================
    // INSCRIPTIONS - Manager, Secrétaire
    // ==========================================
    Route::middleware(['auth:api', 'scope:Manager,Secretaire'])->group(function () {
        Route::apiResource('inscriptions', InscriptionController::class);
        Route::get('inscriptions/etudiant/{etudiantId}', [InscriptionController::class, 'parEtudiant']);
        Route::get('inscriptions/classe/{classeId}', [InscriptionController::class, 'parClasse']);
        Route::patch('inscriptions/{id}/valider', [InscriptionController::class, 'valider']);
        Route::patch('inscriptions/{id}/annuler', [InscriptionController::class, 'annuler']);
        Route::get('inscriptions/statut/{statut}', [InscriptionController::class, 'parStatut']);
    });


    // ==========================================
    // ABSENCES - Professeur, Manager
    // ==========================================

    Route::middleware(['auth:api', 'scope:Professeur,Manager'])->group(function () {
        Route::apiResource('absences', AbsenceController::class);
        Route::patch('absences/{id}/justifier', [AbsenceController::class, 'justifier']);
    });

    // GET absences - accessible à tous
    Route::get('absences/etudiant/{etudiantId}', [AbsenceController::class, 'absencesEtudiant']);
    Route::get('absences/classe/{classeId}', [AbsenceController::class, 'absencesClasse']);
    

    // ==========================================
    // CLASSES - Manager, Secrétaire
    // ==========================================
    Route::middleware(['auth:api', 'scope:Manager,Secretaire'])->group(function () {
        Route::apiResource('classes', ClasseController::class);
    });

    //Get classes - accessible à tous
    Route::get('classes', [ClasseController::class, 'index']);  
    Route::get('classes/{id}', [ClasseController::class, 'show']);


    // ==========================================
    // MATIÈRES - Manager
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('matieres', MatiereController::class);
    });
    
    //GET matieres - accessible à tous
    Route::get('matieres', [MatiereController::class, 'index']);
    Route::get('matieres/{id}', [MatiereController::class, 'show']);


    // ==========================================
    // ANNÉES SCOLAIRES - Manager
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('annees-scolaires', AnneeScolaireController::class);
        Route::patch('annees-scolaires/{id}/activer', [AnneeScolaireController::class, 'activer']);
        Route::get('annee-scolaire/actuelle', [AnneeScolaireController::class, 'actuelle']);
    });

    //Get annees-scolaires - accessible à tous
    Route::get('annees-scolaires', [AnneeScolaireController::class, 'index']);
    Route::get('annees-scolaires/{id}', [AnneeScolaireController::class, 'show']);  

    // ==========================================
    // ENSEIGNEMENTS - Manager, Secrétaire
    // ==========================================
    Route::middleware(['auth:api', 'scope:Manager,Secretaire'])->group(function () {
        Route::apiResource('enseignements', EnseignementController::class);
        Route::get('emploi-temps/classe/{classeId}', [EnseignementController::class, 'emploiTempsClasse']);
        Route::get('emploi-temps/professeur/{professeurId}', [EnseignementController::class, 'emploiTempsProfesseur']);
    });

    //Get enseignements - accessible à tous
    Route::get('enseignements', [EnseignementController::class, 'index']);
    Route::get('enseignements/{id}', [EnseignementController::class, 'show']);

    // ==========================================
    // DASHBOARD & STATISTIQUES - Tous
    // ==========================================

    Route::middleware(['auth:api', 'scope:Manager,Secretaire'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/statistiques', [DashboardController::class, 'statistiques']);
        Route::get('dashboard/etudiants-actifs', [DashboardController::class, 'etudiantsActifs']);
        Route::get('dashboard/revenus', [DashboardController::class, 'revenus']);
    });

    // ==========================================
    // RAPPORTS - Manager, Secrétaire
    // ==========================================
    Route::middleware(['auth:api', 'scope:Manager,Secretaire'])->group(function () {
        Route::get('rapports/inscriptions', [InscriptionController::class, 'rapportInscriptions']);
        Route::get('rapports/paiements', [PaiementController::class, 'rapportPaiements']);
        Route::get('rapports/absences', [AbsenceController::class, 'rapportAbsences']);
        Route::get('rapports/notes', [NoteController::class, 'rapportNotes']);
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
});
