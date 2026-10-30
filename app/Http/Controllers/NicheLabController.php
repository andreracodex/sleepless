<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\NicheCandidate;
use App\Models\NicheEvaluation;
use App\Models\NicheScoreSnapshot;
use App\Services\CollabPalsCaptureService;
use App\Services\NicheEvaluationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NicheLabController extends Controller
{
    public function __construct(
        protected CollabPalsCaptureService $captureService,
        protected NicheEvaluationService $evaluationService
    ) {}

    /**
     * Display Niche Lab comparison dashboard & full CollabPals directory
     */
    public function index(Request $request): View
    {
        $ev = NicheCandidate::with(['latestVerifiedSnapshot', 'latestSnapshot', 'latestEvaluation'])
            ->where('slug', 'electric-vehicles')
            ->firstOrFail();

        $sleep = NicheCandidate::with(['latestVerifiedSnapshot', 'latestSnapshot', 'latestEvaluation'])
            ->where('slug', 'sleep-content')
            ->firstOrFail();

        $comparison = $this->evaluationService->compare($ev, $sleep);

        // Active selected pilot niche
        $selectedNiche = NicheCandidate::with(['latestVerifiedSnapshot', 'latestSnapshot', 'latestEvaluation'])
            ->where('status', 'selected')
            ->first() ?? $ev;

        // Query all niche candidates for creator directory
        $query = NicheCandidate::with(['latestVerifiedSnapshot', 'latestSnapshot']);

        // Search filter (keyword in name, description, slug)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('audience_profile->category', $category);
        }

        // Faceless friendly filter
        if ($request->has('faceless') && $request->input('faceless') !== '' && $request->input('faceless') !== null) {
            $facelessVal = (bool) $request->input('faceless');
            $query->whereHas('latestVerifiedSnapshot', function ($q) use ($facelessVal) {
                $q->where('faceless_friendly', $facelessVal);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'score_desc');
        if ($sort === 'score_desc') {
            $sub = NicheScoreSnapshot::select('niche_score')
                ->whereColumn('niche_candidate_id', 'niche_candidates.id')
                ->where('verification_status', 'verified')
                ->latest('captured_at')
                ->limit(1);
            $query->orderByDesc($sub);
        } elseif ($sort === 'score_asc') {
            $sub = NicheScoreSnapshot::select('niche_score')
                ->whereColumn('niche_candidate_id', 'niche_candidates.id')
                ->where('verification_status', 'verified')
                ->latest('captured_at')
                ->limit(1);
            $query->orderBy($sub);
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderByDesc('status')->orderBy('name', 'asc');
        }

        $perPage = $request->input('per_page', 24);
        if ($perPage === 'all') {
            $allNiches = $query->paginate(250)->withQueryString();
        } else {
            $allNiches = $query->paginate((int) $perPage)->withQueryString();
        }

        $categories = [
            'Finance', 'Technology', 'Education', 'Automotive', 'Hobby', 
            'DIY', 'Lifestyle', 'Sports', 'Gaming', 'Food', 
            'Fitness', 'Beauty', 'Music', 'Travel', 'True Crime', 'ASMR'
        ];

        $recentSnapshots = NicheScoreSnapshot::with('nicheCandidate')
            ->latest('captured_at')
            ->limit(10)
            ->get();

        $auditLogs = AuditLog::latest('created_at')
            ->limit(10)
            ->get();

        return view('niche_lab.index', compact(
            'ev',
            'sleep',
            'comparison',
            'selectedNiche',
            'allNiches',
            'categories',
            'recentSnapshots',
            'auditLogs'
        ));
    }

    /**
     * Trigger synchronization to fetch latest data for all niches from CollabPals
     */
    public function syncAll(Request $request): RedirectResponse
    {
        @set_time_limit(120);

        try {
            Artisan::call('niche:sync-all', ['--fresh' => true]);

            AuditLog::log(
                action: 'sync_collabpals_directory',
                entityType: 'NicheCandidate',
                entityId: 0,
                after: ['synced_at' => now()->toDateTimeString()],
                actorType: 'creator'
            );

            return back()->with('success', "Sinkronisasi berhasil! Seluruh data 200+ niche telah diperbarui dengan data live terbaru dari CollabPals.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal melakukan sinkronisasi CollabPals: " . $e->getMessage());
        }
    }

    /**
     * Trigger live CollabPals capture via Playwright
     */
    public function capture(NicheCandidate $nicheCandidate): RedirectResponse
    {
        try {
            $snapshot = $this->captureService->capture($nicheCandidate);
            return back()->with('success', "Berhasil mengambil skor CollabPals untuk {$nicheCandidate->name}. Skor: {$snapshot->niche_score} (Status: {$snapshot->verification_status})");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal melakukan capture CollabPals: " . $e->getMessage());
        }
    }

    /**
     * Manual score entry with evidence screenshot
     */
    public function manualSnapshot(Request $request, NicheCandidate $nicheCandidate): RedirectResponse
    {
        $validated = $request->validate([
            'niche_score' => 'required|integer|min:0|max:100',
            'cpm_range' => 'nullable|string|max:50',
            'growth_trend' => 'nullable|string|in:Rising,Stable,Declining,Unknown',
            'equipment_level' => 'nullable|string|in:Phone,Basic,Professional,Unknown',
            'faceless_friendly' => 'nullable|boolean',
            'evidence_screenshot' => 'required|image|max:10240', // 10MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('evidence_screenshot')->store('evidence', 'public');

        $snapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $nicheCandidate->id,
            'source_name' => 'CollabPals YouTube Niche Finder',
            'source_url' => 'https://www.collabpals.com/tools/youtube-niche-finder',
            'matched_query' => $nicheCandidate->name,
            'source_niche_name' => $nicheCandidate->name,
            'niche_score' => $validated['niche_score'],
            'cpm_range' => $validated['cpm_range'] ?? null,
            'growth_trend' => $validated['growth_trend'] ?? null,
            'equipment_level' => $validated['equipment_level'] ?? null,
            'faceless_friendly' => $request->boolean('faceless_friendly'),
            'evidence_screenshot_path' => "storage/{$path}",
            'capture_method' => 'manual',
            'verification_status' => 'needs_review',
            'captured_at' => now(),
            'notes' => $validated['notes'] ?? 'Input manual oleh admin.',
        ]);

        AuditLog::log(
            action: 'manual_score_entry',
            entityType: 'NicheScoreSnapshot',
            entityId: $snapshot->id,
            after: $snapshot->toArray(),
            actorType: 'admin'
        );

        return back()->with('success', "Input manual tersimpan dengan status 'needs_review'. Silakan lakukan verifikasi.");
    }

    /**
     * Reviewer verifies or rejects a snapshot
     */
    public function verify(Request $request, NicheScoreSnapshot $snapshot): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,unverified',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $snapshot->verification_status;
        $snapshot->update([
            'verification_status' => $validated['status'],
            'verified_by' => 'Owner Admin',
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? $snapshot->notes,
        ]);

        AuditLog::log(
            action: 'verify_score_snapshot',
            entityType: 'NicheScoreSnapshot',
            entityId: $snapshot->id,
            before: ['status' => $oldStatus],
            after: ['status' => $validated['status']],
            actorType: 'reviewer'
        );

        return back()->with('success', "Snapshot #{$snapshot->id} berhasil diupdate menjadi {$validated['status']}.");
    }

    /**
     * Submit Internal Pilot Score evaluation (Lapisan B)
     */
    public function evaluate(Request $request, NicheCandidate $nicheCandidate): RedirectResponse
    {
        $validated = $request->validate([
            'snapshot_id' => 'required|exists:niche_score_snapshots,id',
            'feasibility_score' => 'required|numeric|min:0|max:100',
            'audience_validation_score' => 'required|numeric|min:0|max:100',
            'production_economics_score' => 'required|numeric|min:0|max:100',
            'monetization_diversification_score' => 'required|numeric|min:0|max:100',
        ]);

        $snapshot = NicheScoreSnapshot::findOrFail($validated['snapshot_id']);

        try {
            $evaluation = $this->evaluationService->evaluate(
                $nicheCandidate,
                $snapshot,
                (float) $validated['feasibility_score'],
                (float) $validated['audience_validation_score'],
                (float) $validated['production_economics_score'],
                (float) $validated['monetization_diversification_score'],
                'Owner Admin'
            );

            return back()->with('success', "Evaluasi internal berhasil disimpan! Skor Internal: {$evaluation->internal_score} (Keputusan: {$evaluation->decision})");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal melakukan evaluasi: " . $e->getMessage());
        }
    }

    /**
     * Owner selection of the pilot niche
     */
    public function select(NicheCandidate $nicheCandidate): RedirectResponse
    {
        $verifiedSnapshot = $nicheCandidate->latestVerifiedSnapshot;
        if (!$verifiedSnapshot) {
            return back()->with('error', "Tidak dapat memilih niche {$nicheCandidate->name} karena belum memiliki skor CollabPals yang terverifikasi (PRD FR-001).");
        }

        // Set this candidate as selected
        $nicheCandidate->update(['status' => 'selected']);

        // Set other candidates as candidate
        NicheCandidate::where('id', '!=', $nicheCandidate->id)->update(['status' => 'candidate']);

        AuditLog::log(
            action: 'select_pilot_niche',
            entityType: 'NicheCandidate',
            entityId: $nicheCandidate->id,
            after: ['status' => 'selected'],
            actorType: 'owner'
        );

        return back()->with('success', "Niche [{$nicheCandidate->name}] telah resmi ditetapkan sebagai Pilot Channel oleh Owner!");
    }
}

