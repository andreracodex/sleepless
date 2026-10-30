<?php

use App\Http\Controllers\IdeaBoardController;
use App\Http\Controllers\NicheLabController;
use App\Http\Controllers\ProductionStudioController;
use App\Http\Controllers\ScriptController;
use Illuminate\Support\Facades\Route;

// Niche Lab (Phase 1)
Route::get('/', [NicheLabController::class, 'index'])->name('niches.index');

Route::prefix('niches')->group(function () {
    Route::post('/sync', [NicheLabController::class, 'syncAll'])->name('niches.sync');
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

// Scriptwriting & Audio Studio (Phase 3)
Route::get('/ideas/{idea}/script', [ScriptController::class, 'show'])->name('scripts.show');
Route::post('/ideas/{idea}/script/generate', [ScriptController::class, 'generate'])->name('scripts.generate');
Route::post('/scripts/{script}/approve', [ScriptController::class, 'approve'])->name('scripts.approve');
Route::post('/scripts/{script}/audio', [ScriptController::class, 'generateAudio'])->name('scripts.audio');

// Video Production & YouTube Publishing (Phase 4 & 5)
Route::get('/production', [ProductionStudioController::class, 'index'])->name('production.index');
Route::post('/scripts/{script}/render', [ProductionStudioController::class, 'render'])->name('production.render');
Route::post('/production/{job}/upload', [ProductionStudioController::class, 'upload'])->name('production.upload');
Route::post('/production/upload/{upload}/publish', [ProductionStudioController::class, 'publishApproval'])->name('production.publish');
