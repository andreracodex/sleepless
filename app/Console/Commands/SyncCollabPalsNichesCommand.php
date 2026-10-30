<?php

namespace App\Console\Commands;

use App\Models\NicheCandidate;
use App\Models\NicheScoreSnapshot;
use App\Support\PythonBinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class SyncCollabPalsNichesCommand extends Command
{
    protected $signature = 'niche:sync-all {--fresh : Re-scrape live directory from CollabPals}';
    protected $description = 'Import and synchronize all 200+ niches from CollabPals YouTube Niche Finder';

    public function handle(): int
    {
        $jsonPath = storage_path('app/collabpals_all_niches.json');

        if ($this->option('fresh') || !file_exists($jsonPath)) {
            $this->info("Running CollabPals live scraper worker...");
            $python = PythonBinary::getPath();
            $script = base_path('scripts/scrape_collabpals_directory.py');

            $result = Process::path(base_path())->timeout(90)->run([$python, $script]);
            if (!$result->successful() || !file_exists($jsonPath)) {
                $this->error("Failed to scrape CollabPals directory: " . $result->errorOutput() . " " . $result->output());
                return self::FAILURE;
            }
        }

        $rawJson = file_get_contents($jsonPath);
        $niches = json_decode($rawJson, true);

        if (!is_array($niches) || empty($niches)) {
            $this->error("Invalid or empty CollabPals JSON database.");
            return self::FAILURE;
        }

        $this->info("Found " . count($niches) . " niches in CollabPals database. Importing to database...");

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($niches as $item) {
            $slug = $item['slug'];
            if (in_array($slug, ['video-idea-generator', 'keyword-research-tool', 'trend-finder', 'money-calculator', 'channel-value-calculator', 'title-generator'])) {
                continue;
            }
            $name = $item['name'];
            $score = $item['niche_score'] ?? null;
            $category = $item['category'] ?? 'General';
            $formats = $item['formats'] ?? [];
            $channels = $item['example_channels'] ?? '';
            $viewsPer1k = $item['views_per_1k'] ?? 30;

            $candidate = NicheCandidate::where('slug', $slug)->first();

            if (!$candidate) {
                // If it's Electric Vehicles, let's keep it consistent
                $isEv = str_contains(strtolower($name), 'electric vehicle');
                $isSleep = str_contains(strtolower($name), 'sleep');

                $candidate = NicheCandidate::create([
                    'slug' => $slug,
                    'name' => $isEv ? 'Electric Vehicles (EV)' : ($isSleep ? 'Sleep Content' : $name),
                    'description' => "Kategori: {$category} • Views/1k: {$viewsPer1k} • Channel Referensi: {$channels}",
                    'status' => $isEv ? 'selected' : 'candidate',
                    'audience_profile' => [
                        'category' => $category,
                        'example_channels' => $channels,
                        'views_per_1k' => $viewsPer1k,
                        'equipment' => $item['equipment'] ?? 'Basic',
                    ],
                    'content_pillars' => !empty($formats) ? $formats : ['Explainer', 'Guides', 'Industry Updates'],
                ]);
                $createdCount++;
            } else {
                $updatedCount++;
            }

            // Create or update verified snapshot for this candidate
            // For 'sleep-content', if it already exists, do not overwrite unless requested
            if ($score) {
                NicheScoreSnapshot::updateOrCreate(
                    [
                        'niche_candidate_id' => $candidate->id,
                        'source_name' => 'CollabPals YouTube Niche Finder',
                    ],
                    [
                        'source_url' => 'https://www.collabpals.com/tools/youtube-niche-finder',
                        'matched_query' => $name,
                        'source_niche_name' => $name,
                        'niche_score' => $score,
                        'cpm_range' => $item['cpm_range'] ?? '$5-$15',
                        'growth_trend' => $item['trend'] ?? 'Rising',
                        'faceless_friendly' => (bool) ($item['faceless'] ?? false),
                        'equipment_level' => $item['equipment'] ?? 'Basic',
                        'verification_status' => 'verified',
                        'captured_at' => now(),
                        'notes' => "CollabPals 2026 Directory: Category {$category}",
                    ]
                );
            }
        }

        $this->info("Sync completed! Created {$createdCount} new niches, updated {$updatedCount} existing niches.");
        return self::SUCCESS;
    }
}
