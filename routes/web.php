<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CvAnalysisController;
use App\Http\Controllers\CvDocumentController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\InterviewEventController;
use App\Http\Controllers\InterviewReportController;
use App\Http\Controllers\InterviewScriptController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruitmentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', RecruitmentDashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/supprimer', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Candidats + CVthèque ----------
    Route::get('/candidats', [CandidateController::class, 'index'])->name('candidates.index');
    Route::post('/candidats', [CandidateController::class, 'store'])->name('candidates.store');
    Route::get('/candidats/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
    Route::post('/candidats/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
    Route::post('/candidats/{candidate}/supprimer', [CandidateController::class, 'destroy'])->name('candidates.destroy');

    // ---------- CV upload/download ----------
    Route::post('/candidats/{candidate}/cv', [CvDocumentController::class, 'store'])->name('cv.store');
    Route::get('/cv/{cvDocument}/telecharger', [CvDocumentController::class, 'download'])->name('cv.download');
    Route::post('/cv/{cvDocument}/supprimer', [CvDocumentController::class, 'destroy'])->name('cv.destroy');

    // ---------- Analyses IA ----------
    Route::get('/analyses', [CvAnalysisController::class, 'index'])->name('analyses.index');
    Route::post('/analyses', [CvAnalysisController::class, 'store'])->name('analyses.store');
    Route::post('/analyses/comparer', [CvAnalysisController::class, 'compare'])->name('analyses.compare');
    Route::post('/analyses/{analysis}/supprimer', [CvAnalysisController::class, 'destroy'])->name('analyses.destroy');

    // ---------- Offres d'emploi ----------
    Route::get('/offres', [JobOfferController::class, 'index'])->name('offers.index');
    Route::post('/offres', [JobOfferController::class, 'store'])->name('offers.store');
    Route::get('/offres/{jobOffer}', [JobOfferController::class, 'show'])->name('offers.show');
    Route::post('/offres/{jobOffer}', [JobOfferController::class, 'update'])->name('offers.update');
    Route::post('/offres/{jobOffer}/archiver', [JobOfferController::class, 'archive'])->name('offers.archive');
    Route::post('/offres/{jobOffer}/supprimer', [JobOfferController::class, 'destroy'])->name('offers.destroy');

    // ---------- Comptes-rendus d'entretien ----------
    Route::get('/comptes-rendus', [InterviewReportController::class, 'index'])->name('reports.index');
    Route::post('/comptes-rendus', [InterviewReportController::class, 'store'])->name('reports.store');
    Route::post('/comptes-rendus/{report}', [InterviewReportController::class, 'update'])->name('reports.update');
    Route::post('/comptes-rendus/{report}/supprimer', [InterviewReportController::class, 'destroy'])->name('reports.destroy');

    // ---------- Scripts d'entretien ----------
    Route::get('/scripts', [InterviewScriptController::class, 'index'])->name('scripts.index');
    Route::post('/scripts', [InterviewScriptController::class, 'store'])->name('scripts.store');
    Route::get('/scripts/{script}', [InterviewScriptController::class, 'show'])->name('scripts.show');
    Route::post('/scripts/{script}', [InterviewScriptController::class, 'update'])->name('scripts.update');
    Route::post('/scripts/{script}/supprimer', [InterviewScriptController::class, 'destroy'])->name('scripts.destroy');

    // ---------- Emails ----------
    Route::get('/emails', [EmailTemplateController::class, 'index'])->name('emails.index');
    Route::post('/emails/modeles', [EmailTemplateController::class, 'storeTemplate'])->name('emails.templates.store');
    Route::post('/emails/modeles/{template}', [EmailTemplateController::class, 'updateTemplate'])->name('emails.templates.update');
    Route::post('/emails/modeles/{template}/supprimer', [EmailTemplateController::class, 'destroyTemplate'])->name('emails.templates.destroy');
    Route::post('/emails/envoyer', [EmailTemplateController::class, 'send'])->name('emails.send');

    // ---------- Agenda ----------
    Route::get('/agenda', [InterviewEventController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [InterviewEventController::class, 'store'])->name('agenda.store');
    Route::post('/agenda/{event}', [InterviewEventController::class, 'update'])->name('agenda.update');
    Route::post('/agenda/{event}/supprimer', [InterviewEventController::class, 'destroy'])->name('agenda.destroy');
});

require __DIR__.'/auth.php';
