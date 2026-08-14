<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\BulletinDetailController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\JournalAuditController;

// ============================================
// AUTHENTIFICATION
// ============================================

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// ============================================
// MODULE ACADÉMIQUE (protégé)
// ============================================

// --- Lecture : accessible à tout utilisateur authentifié ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/annees-scolaires', [AnneeScolaireController::class, 'index']);
    Route::get('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'show']);

    Route::get('/niveaux', [NiveauController::class, 'index']);
    Route::get('/niveaux/{niveau}', [NiveauController::class, 'show']);

    Route::get('/filieres', [FiliereController::class, 'index']);
    Route::get('/filieres/{filiere}', [FiliereController::class, 'show']);

    Route::get('/classes', [ClasseController::class, 'index']);
    Route::get('/classes/{classe}', [ClasseController::class, 'show']);

    Route::get('/eleves', [EleveController::class, 'index']);
    Route::get('/eleves/{eleve}', [EleveController::class, 'show']);

    Route::get('/inscriptions', [InscriptionController::class, 'index']);
    Route::get('/inscriptions/{inscription}', [InscriptionController::class, 'show']);
});

// --- Écriture : réservée à l'admin ---
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/annees-scolaires', [AnneeScolaireController::class, 'store']);
    Route::put('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'update']);
    Route::delete('/annees-scolaires/{anneeScolaire}', [AnneeScolaireController::class, 'destroy']);

    Route::post('/niveaux', [NiveauController::class, 'store']);
    Route::put('/niveaux/{niveau}', [NiveauController::class, 'update']);
    Route::delete('/niveaux/{niveau}', [NiveauController::class, 'destroy']);

    Route::post('/filieres', [FiliereController::class, 'store']);
    Route::put('/filieres/{filiere}', [FiliereController::class, 'update']);
    Route::delete('/filieres/{filiere}', [FiliereController::class, 'destroy']);

    Route::post('/classes', [ClasseController::class, 'store']);
    Route::put('/classes/{classe}', [ClasseController::class, 'update']);
    Route::delete('/classes/{classe}', [ClasseController::class, 'destroy']);

    Route::post('/eleves', [EleveController::class, 'store']);
    Route::put('/eleves/{eleve}', [EleveController::class, 'update']);
    Route::delete('/eleves/{eleve}', [EleveController::class, 'destroy']);
});

// --- Écriture inscriptions : permission dédiée ---
Route::middleware(['auth:sanctum', 'permission:gerer-inscriptions'])->group(function () {
    Route::post('/inscriptions', [InscriptionController::class, 'store']);
    Route::put('/inscriptions/{inscription}', [InscriptionController::class, 'update']);
    Route::delete('/inscriptions/{inscription}', [InscriptionController::class, 'destroy']);
});

// ============================================
// MODULE PÉDAGOGIQUE (protégé)
// ============================================

// --- Lecture : accessible à tout utilisateur authentifié ---
// (matieres, salles, enseignants = référentiel commun, pas de confidentialité)
// (affectations, cours = filtrage géré DANS le contrôleur selon le rôle)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/matieres', [MatiereController::class, 'index']);
    Route::get('/matieres/{matiere}', [MatiereController::class, 'show']);

    Route::get('/salles', [SalleController::class, 'index']);
    Route::get('/salles/{salle}', [SalleController::class, 'show']);

    Route::get('/enseignants', [EnseignantController::class, 'index']);
    Route::get('/enseignants/{enseignant}', [EnseignantController::class, 'show']);

    Route::get('/affectations', [AffectationController::class, 'index']);
    Route::get('/affectations/{affectation}', [AffectationController::class, 'show']);

    Route::get('/cours', [CoursController::class, 'index']);
    Route::get('/cours/{cours}', [CoursController::class, 'show']);
});

// --- Écriture : réservée à l'admin ---
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/matieres', [MatiereController::class, 'store']);
    Route::put('/matieres/{matiere}', [MatiereController::class, 'update']);
    Route::delete('/matieres/{matiere}', [MatiereController::class, 'destroy']);

    Route::post('/salles', [SalleController::class, 'store']);
    Route::put('/salles/{salle}', [SalleController::class, 'update']);
    Route::delete('/salles/{salle}', [SalleController::class, 'destroy']);

    Route::post('/enseignants', [EnseignantController::class, 'store']);
    Route::put('/enseignants/{enseignant}', [EnseignantController::class, 'update']);
    Route::delete('/enseignants/{enseignant}', [EnseignantController::class, 'destroy']);

    Route::post('/affectations', [AffectationController::class, 'store']);
    Route::put('/affectations/{affectation}', [AffectationController::class, 'update']);
    Route::delete('/affectations/{affectation}', [AffectationController::class, 'destroy']);

    Route::post('/cours', [CoursController::class, 'store']);
    Route::put('/cours/{cours}', [CoursController::class, 'update']);
    Route::delete('/cours/{cours}', [CoursController::class, 'destroy']);
});

// ============================================
// MODULE ÉVALUATIONS (pas encore protégé)
// ============================================

Route::get('/examens', [ExamenController::class, 'index']);
Route::post('/examens', [ExamenController::class, 'store']);
Route::get('/examens/{examen}', [ExamenController::class, 'show']);
Route::put('/examens/{examen}', [ExamenController::class, 'update']);
Route::delete('/examens/{examen}', [ExamenController::class, 'destroy']);

Route::get('/notes', [NoteController::class, 'index']);
Route::post('/notes', [NoteController::class, 'store']);
Route::get('/notes/{note}', [NoteController::class, 'show']);
Route::put('/notes/{note}', [NoteController::class, 'update']);
Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

Route::get('/bulletins', [BulletinController::class, 'index']);
Route::post('/bulletins', [BulletinController::class, 'store']);
Route::get('/bulletins/{bulletin}', [BulletinController::class, 'show']);
Route::put('/bulletins/{bulletin}', [BulletinController::class, 'update']);
Route::delete('/bulletins/{bulletin}', [BulletinController::class, 'destroy']);

Route::get('/bulletin-details', [BulletinDetailController::class, 'index']);
Route::post('/bulletin-details', [BulletinDetailController::class, 'store']);
Route::get('/bulletin-details/{bulletinDetail}', [BulletinDetailController::class, 'show']);
Route::put('/bulletin-details/{bulletinDetail}', [BulletinDetailController::class, 'update']);
Route::delete('/bulletin-details/{bulletinDetail}', [BulletinDetailController::class, 'destroy']);

// ============================================
// MODULE VIE SCOLAIRE (pas encore protégé)
// ============================================

Route::get('/presences', [PresenceController::class, 'index']);
Route::post('/presences', [PresenceController::class, 'store']);
Route::get('/presences/{presence}', [PresenceController::class, 'show']);
Route::put('/presences/{presence}', [PresenceController::class, 'update']);
Route::delete('/presences/{presence}', [PresenceController::class, 'destroy']);

// ============================================
// MODULE AUDIT (pas encore protégé)
// ============================================

Route::get('/journal-audits', [JournalAuditController::class, 'index']);
Route::post('/journal-audits', [JournalAuditController::class, 'store']);
Route::get('/journal-audits/{journalAudit}', [JournalAuditController::class, 'show']);
Route::put('/journal-audits/{journalAudit}', [JournalAuditController::class, 'update']);
Route::delete('/journal-audits/{journalAudit}', [JournalAuditController::class, 'destroy']);