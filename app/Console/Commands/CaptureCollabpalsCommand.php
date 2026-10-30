<?php

namespace App\Console\Commands;

use App\Models\NicheCandidate;
use App\Services\CollabPalsCaptureService;
use Illuminate\Console\Command;

class CaptureCollabpalsCommand extends Command
{
    protected $signature = 'niche:capture {slug? : Slug of the niche candidate (electric-vehicles or sleep-content)}';
    protected $description = 'Trigger Playwright browser worker to capture live CollabPals scores and evidence for niche candidates';

    public function handle(CollabPalsCaptureService $service): int
    {
        $slug = $this->argument('slug');

        $query = NicheCandidate::query();
        if ($slug) {
            $query->where('slug', $slug);
        }

        $candidates = $query->get();

        if ($candidates->isEmpty()) {
            $this->error("No niche candidates found matching criteria.");
            return self::FAILURE;
        }

        foreach ($candidates as $candidate) {
            $this->info("Starting CollabPals live capture for: {$candidate->name} ({$candidate->slug})...");

            try {
                $snapshot = $service->capture($candidate);
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Niche Name', $snapshot->source_niche_name],
                        ['Score', $snapshot->niche_score ?? 'null'],
                        ['CPM Range', $snapshot->cpm_range ?? 'null'],
                        ['Growth Trend', $snapshot->growth_trend ?? 'null'],
                        ['Equipment', $snapshot->equipment_level ?? 'null'],
                        ['Faceless', $snapshot->faceless_friendly ? 'Yes' : 'No'],
                        ['Status', $snapshot->verification_status],
                        ['Screenshot', $snapshot->evidence_screenshot_path],
                        ['Captured At', $snapshot->captured_at->toDateTimeString()],
                    ]
                );
                $this->info("Capture successful for {$candidate->name}!");
            } catch (\Exception $e) {
                $this->error("Capture failed for {$candidate->name}: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
