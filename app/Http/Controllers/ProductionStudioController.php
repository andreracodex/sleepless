<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ContentIdea;
use App\Models\ProductionJob;
use App\Models\Script;
use App\Models\YoutubeUpload;
use App\Services\VideoRenderingService;
use App\Services\YouTubeUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionStudioController extends Controller
{
    public function __construct(
        protected VideoRenderingService $renderingService,
        protected YouTubeUploadService $uploadService
    ) {}

    /**
     * Display Production Studio dashboard & QA reports
     */
    public function index(): View
    {
        $jobs = ProductionJob::with(['contentIdea', 'script', 'channel', 'youtubeUpload'])
            ->latest('created_at')
            ->get();

        return view('production.index', compact('jobs'));
    }

    /**
     * Render a video job from an approved script
     */
    public function render(Request $request, Script $script): RedirectResponse
    {
        if (!$script->isApproved()) {
            return back()->with('error', "Naskah harus disetujui (Approved) sebelum proses rendering video dapat dimulai (PRD FR-010).");
        }

        $profile = $request->input('profile', $script->contentIdea->format === 'shorts' ? 'shorts_vertical_1080p' : 'youtube_landscape_1080p');

        try {
            $job = $this->renderingService->render($script, $profile);
            return redirect()->route('production.index')->with('success', "Render video #{$job->id} selesai dengan sukses! QA checks passed.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal melakukan render video: " . $e->getMessage());
        }
    }

    /**
     * Trigger YouTube private upload
     */
    public function upload(ProductionJob $job): RedirectResponse
    {
        try {
            $upload = $this->uploadService->uploadPrivate($job);
            return back()->with('success', "Video berhasil diunggah ke YouTube sebagai PRIVATE! Video ID: {$upload->youtube_video_id}");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal upload YouTube: " . $e->getMessage());
        }
    }

    /**
     * Owner approves changing video from Private to Scheduled / Public
     */
    public function publishApproval(Request $request, YoutubeUpload $upload): RedirectResponse
    {
        $validated = $request->validate([
            'privacy_status' => 'required|in:public,unlisted,private',
        ]);

        $upload->update([
            'privacy_status' => $validated['privacy_status'],
            'published_at' => $validated['privacy_status'] === 'public' ? now() : null,
        ]);

        AuditLog::log(
            action: 'owner_approve_youtube_publish',
            entityType: 'YoutubeUpload',
            entityId: $upload->id,
            after: ['privacy_status' => $validated['privacy_status']],
            actorType: 'owner'
        );

        return back()->with('success', "Status visibilitas video diupdate menjadi '{$validated['privacy_status']}'.");
    }
}
