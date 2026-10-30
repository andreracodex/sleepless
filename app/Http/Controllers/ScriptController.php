<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ContentIdea;
use App\Models\MediaAsset;
use App\Models\ResearchDossier;
use App\Models\Script;
use App\Services\ScriptGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScriptController extends Controller
{
    public function __construct(
        protected ScriptGenerationService $scriptService
    ) {}

    /**
     * Generate or view the script for a given Content Idea
     */
    public function show(Request $request, ContentIdea $idea): View
    {
        $idea->load(['channel', 'researchDossier']);
        $dossier = $idea->researchDossier;

        $supportedLanguages = ScriptGenerationService::supportedLanguages();

        // Support switching between versions if multiple exist
        $version = $request->query('version');
        if ($version) {
            $script = Script::where('content_idea_id', $idea->id)->where('version', $version)->first();
        } else {
            $script = Script::where('content_idea_id', $idea->id)->latest('version')->first();
        }

        $allVersions = Script::where('content_idea_id', $idea->id)->orderBy('version', 'desc')->get();

        $voiceoverAsset = null;
        if ($script) {
            $voiceoverAsset = MediaAsset::where('asset_type', 'audio_voiceover')
                ->whereJsonContains('metadata->script_id', $script->id)
                ->latest()
                ->first();
        }

        return view('scripts.show', compact('idea', 'dossier', 'script', 'allVersions', 'voiceoverAsset', 'supportedLanguages'));
    }

    /**
     * Trigger script generation from research dossier in chosen language & voice
     */
    public function generate(Request $request, ContentIdea $idea): RedirectResponse
    {
        $dossier = $idea->researchDossier;
        if (!$dossier || !$dossier->isApproved()) {
            return back()->with('error', "Naskah tidak dapat di-generate sebelum Research Dossier disetujui (PRD FR-007).");
        }

        $language = $request->input('language', 'id');
        $voice = $request->input('voice');

        $supportedLanguages = ScriptGenerationService::supportedLanguages();
        if (!array_key_exists($language, $supportedLanguages)) {
            $language = 'id';
        }

        try {
            $script = $this->scriptService->generateScript($idea, $dossier, $language, $voice);
            $langName = $supportedLanguages[$language]['name'] ?? strtoupper($language);
            return back()->with('success', "Naskah versi v{$script->version} ({$langName}) berhasil dibuat dengan {$script->word_count} kata dan " . count($script->script_json['scenes'] ?? []) . " adegan terstruktur.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal membuat naskah: " . $e->getMessage());
        }
    }

    /**
     * Reviewer approves or requests changes to the script
     */
    public function approve(Request $request, Script $script): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,needs_revision,reject',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $script->status;
        $newStatus = match ($validated['action']) {
            'approve' => 'approved',
            'needs_revision' => 'needs_review',
            'reject' => 'rejected',
        };

        $script->update([
            'status' => $newStatus,
            'approved_by' => 'Senior Script Editor',
            'approved_at' => $newStatus === 'approved' ? now() : null,
        ]);

        if ($newStatus === 'approved') {
            $script->contentIdea->update(['status' => 'production']);
        }

        AuditLog::log(
            action: 'approve_video_script',
            entityType: 'Script',
            entityId: $script->id,
            before: ['status' => $oldStatus],
            after: ['status' => $newStatus],
            actorType: 'reviewer'
        );

        return back()->with('success', "Status naskah diupdate menjadi: '{$newStatus}'.");
    }

    /**
     * Generate Neural TTS Voiceover Audio for the approved script
     */
    public function generateAudio(Request $request, Script $script): RedirectResponse
    {
        $voice = $request->input('voice');

        try {
            $asset = $this->scriptService->generateVoiceoverAudio($script, $voice);
            $chosenVoice = $asset->metadata['voice'] ?? 'Edge Neural TTS';
            return back()->with('success', "Voiceover audio neural ({$chosenVoice}) berhasil digenerate dan disimpan ke Media Library!");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal men-generate audio: " . $e->getMessage());
        }
    }
}
