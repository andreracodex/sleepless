<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\NicheCandidate;
use App\Models\NicheScoreSnapshot;
use Exception;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class CollabPalsCaptureService
{
    /**
     * Run the Playwright browser worker to capture live CollabPals niche metrics
     */
    public function capture(NicheCandidate $candidate): NicheScoreSnapshot
    {
        $query = $candidate->name;
        // Strip parenthetical suffix if present, e.g. "Electric Vehicles (EV)" -> "Electric Vehicles"
        $cleanQuery = preg_replace('/\s*\(.*?\)\s*/', '', $query);

        $scriptPath = base_path('scripts/capture_collabpals.py');
        $command = "python \"{$scriptPath}\" \"{$cleanQuery}\"";

        $result = Process::path(base_path())
            ->timeout(60)
            ->run($command);

        $output = $result->output();

        // Extract JSON from output (ignoring initial log lines)
        $jsonStart = strpos($output, '{');
        if ($jsonStart === false) {
            throw new RuntimeException("Capture failed: no JSON found in output. Error: " . $result->errorOutput() . " Output: " . $output);
        }

        $jsonStr = substr($output, $jsonStart);
        $data = json_decode($jsonStr, true);

        if (!$data || !isset($data['niche_score'])) {
            throw new RuntimeException("Capture parser failed: invalid JSON structure. Output: " . $jsonStr);
        }

        $snapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $candidate->id,
            'source_name' => $data['source_name'] ?? 'CollabPals YouTube Niche Finder',
            'source_url' => $data['source_url'] ?? 'https://www.collabpals.com/tools/youtube-niche-finder',
            'matched_query' => $data['matched_query'] ?? $cleanQuery,
            'source_niche_name' => $data['source_niche_name'] ?? $cleanQuery,
            'niche_score' => $data['niche_score'] ?? null,
            'demand_score' => $data['demand_score'] ?? null,
            'competition_score' => $data['competition_score'] ?? null,
            'monetization_score' => $data['monetization_score'] ?? null,
            'cpm_range' => $data['cpm_range'] ?? null,
            'growth_trend' => $data['growth_trend'] ?? null,
            'faceless_friendly' => $data['faceless_friendly'] ?? false,
            'equipment_level' => $data['equipment_level'] ?? null,
            'sub_niches' => $data['sub_niches'] ?? [],
            'evidence_screenshot_path' => $data['evidence_screenshot_path'] ?? null,
            'evidence_text' => $data['evidence_text'] ?? [],
            'capture_method' => $data['capture_method'] ?? 'browser_automation',
            'parser_version' => $data['parser_version'] ?? '1.0.1',
            'confidence' => $data['confidence'] ?? 1.0,
            'verification_status' => $data['verification_status'] ?? 'verified',
            'captured_at' => $data['captured_at'] ?? now(),
            'notes' => $data['notes'] ?? null,
        ]);

        AuditLog::log(
            action: 'capture_collabpals_score',
            entityType: 'NicheScoreSnapshot',
            entityId: $snapshot->id,
            after: [
                'niche' => $candidate->slug,
                'score' => $snapshot->niche_score,
                'status' => $snapshot->verification_status,
                'evidence' => $snapshot->evidence_screenshot_path,
            ],
            actorType: 'worker'
        );

        return $snapshot;
    }
}

