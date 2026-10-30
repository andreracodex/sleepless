<?php

use App\Http\Controllers\IdeaBoardController;
use App\Http\Controllers\NicheLabController;
use Illuminate\Support\Facades\Route;

// Niche Lab (Phase 1)
Route::get('/', [NicheLabController::class, 'index'])->name('niches.index');

Route::prefix('niches')->group(function () {
    Route::post('/{nicheCandidate}/capture', [NicheLabController::class, 'capture'])->name('niches.capture');
    Route::post('/{nicheCandidate}/manual-snapshot', [NicheLabController::class, 'manualSnapshot'])->name('niches.manual');
    Route::post('/{nicheCandidate}/evaluate', [NicheLabController::class, 'evaluate'])->name('niches.evaluate');
    Route::post('/{nicheCandidate}/select', [NicheLabController::class, 'select'])->name('niches.select');
});

Route::post('/snapshots/{snapshot}/verify', [NicheLabController::class, 'verify'])->name('snapshots.verify');

// Content Planning & Ideation (Phase 2)
Route::get('/ideas', [IdeaBoardController::class, 'index'])->name('ideas.index');
Route::post('/ideas', [IdeaBoardController::class, 'store'])->name('ideas.store');
Route::get('/ideas/{idea}/dossier', [IdeaBoardController::class, 'showDossier'])->name('ideas.dossier');
Route::post('/dossier/{dossier}/review', [IdeaBoardController::class, 'reviewDossier'])->name('dossier.review');
