<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ContentIdea;
use App\Models\NicheCandidate;
use App\Models\ResearchDossier;
use App\Services\IdeaGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdeaBoardController extends Controller
{
    /**
     * Display Idea Board and Content Planning dashboard connected to the selected niche
     */
    public function index(Request $request, IdeaGeneratorService $ideaService): View
    {
        // 1. Get the currently selected pilot niche (or fallback to EV)
        $selectedNiche = NicheCandidate::with(['latestVerifiedSnapshot'])
            ->where('status', 'selected')
            ->first()
            ?? NicheCandidate::where('slug', 'electric-vehicles')->first();

        if (!$selectedNiche) {
            $selectedNiche = NicheCandidate::firstOrFail();
        }

        // 2. Ensure channel & ideas are ready for this selected niche
        $channel = $ideaService->ensureChannelAndIdeasForNiche($selectedNiche);

        // Ensure this channel is marked active
        if ($channel->status !== 'active') {
            Channel::where('id', '!=', $channel->id)->update(['status' => 'paused']);
            $channel->update(['status' => 'active']);
        }

        $formatFilter = $request->query('format');
        $statusFilter = $request->query('status');

        $query = $channel->contentIdeas()->with('researchDossier');

        if ($formatFilter) {
            $query->where('format', $formatFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $ideas = $query->get();

        $stats = [
            'total' => $channel->contentIdeas()->count(),
            'long_form' => $channel->contentIdeas()->where('format', 'long_form')->count(),
            'shorts' => $channel->contentIdeas()->where('format', 'shorts')->count(),
            'accepted' => $channel->contentIdeas()->where('status', 'accepted')->count(),
            'researching' => $channel->contentIdeas()->where('status', 'researching')->count(),
            'approved_dossiers' => ResearchDossier::where('status', 'approved')->count(),
        ];

        return view('ideas.index', compact('channel', 'ideas', 'stats', 'formatFilter', 'statusFilter'));
    }

    /**
     * Create a new content idea manually or via AI suggestion
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'title_idea' => 'required|string|max:255',
            'angle' => 'required|string',
            'audience_promise' => 'required|string',
            'format' => 'required|in:long_form,shorts',
            'target_duration_seconds' => 'required|integer|min:15|max:3600',
            'keywords' => 'nullable|string',
            'novelty_notes' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        $channel = Channel::findOrFail($validated['channel_id']);

        $keywords = array_filter(array_map('trim', explode(',', $validated['keywords'] ?? '')));

        $idea = ContentIdea::create([
            'channel_id' => $channel->id,
            'niche_candidate_id' => $channel->niche_candidate_id,
            'title_idea' => $validated['title_idea'],
            'angle' => $validated['angle'],
            'audience_promise' => $validated['audience_promise'],
            'format' => $validated['format'],
            'target_duration_seconds' => (int) $validated['target_duration_seconds'],
            'keyword_set' => $keywords,
            'novelty_notes' => $validated['novelty_notes'] ?? null,
            'status' => 'accepted',
            'priority' => $validated['priority'],
            'created_by' => 'Content Strategist',
        ]);

        AuditLog::log(
            action: 'create_content_idea',
            entityType: 'ContentIdea',
            entityId: $idea->id,
            after: $idea->toArray(),
            actorType: 'creator'
        );

        return back()->with('success', "Ide baru '{$idea->title_idea}' berhasil ditambahkan ke Idea Board!");
    }

    /**
     * Display Research Dossier for a specific content idea
     */
    public function showDossier(ContentIdea $idea): View
    {
        $idea->load(['channel', 'nicheCandidate', 'researchDossier']);
        $dossier = $idea->researchDossier;

        return view('ideas.dossier', compact('idea', 'dossier'));
    }

    /**
     * Approve or reject a research dossier (FR-006 gate)
     */
    public function reviewDossier(Request $request, ResearchDossier $dossier): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,needs_revision,reject',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $dossier->status;
        $newStatus = match ($validated['action']) {
            'approve' => 'approved',
            'needs_revision' => 'needs_review',
            'reject' => 'rejected',
        };

        $dossier->update([
            'status' => $newStatus,
            'reviewer_id' => 'Owner Admin',
            'approved_at' => $newStatus === 'approved' ? now() : null,
        ]);

        if ($newStatus === 'approved') {
            $dossier->contentIdea->update(['status' => 'scripting']);
        }

        AuditLog::log(
            action: 'review_research_dossier',
            entityType: 'ResearchDossier',
            entityId: $dossier->id,
            before: ['status' => $oldStatus],
            after: ['status' => $newStatus, 'notes' => $validated['notes'] ?? null],
            actorType: 'reviewer'
        );

        return back()->with('success', "Status Research Dossier #{$dossier->id} diubah menjadi '{$newStatus}'.");
    }
}

