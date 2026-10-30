<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\NicheCandidate;
use App\Models\NicheEvaluation;
use App\Models\NicheScoreSnapshot;
use App\Services\NicheEvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NicheLabTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NicheCandidateSeeder::class);
    }

    public function test_niche_lab_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Electric Vehicles');
        $response->assertSee('Sleep Content');
        $response->assertSee('PRD Section 3.4 — Decision Engine');
    }

    public function test_decision_engine_calculates_correct_internal_score(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        $snapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals YouTube Niche Finder',
            'source_url' => 'https://www.collabpals.com/tools/youtube-niche-finder',
            'matched_query' => 'Electric Vehicles',
            'source_niche_name' => 'Electric Vehicles',
            'niche_score' => 78,
            'cpm_range' => '$6-$18',
            'verification_status' => 'verified',
            'captured_at' => now(),
        ]);

        $evalService = app(NicheEvaluationService::class);
        $evaluation = $evalService->evaluate(
            candidate: $ev,
            snapshot: $snapshot,
            feasibility: 85,
            audienceValidation: 85,
            productionEconomics: 75,
            monetizationDiversification: 85,
            evaluatedBy: 'Tester'
        );

        // 0.40 * 78 (31.2) + 0.20 * 85 (17.0) + 0.20 * 85 (17.0) + 0.10 * 75 (7.5) + 0.10 * 85 (8.5) = 81.20
        $this->assertEquals(81.20, (float) $evaluation->internal_score);
        $this->assertEquals('recommended', $evaluation->decision);
    }

    public function test_decision_engine_blocks_unverified_snapshot(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        $snapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals YouTube Niche Finder',
            'source_url' => 'https://www.collabpals.com/tools/youtube-niche-finder',
            'matched_query' => 'Electric Vehicles',
            'source_niche_name' => 'Electric Vehicles',
            'niche_score' => 78,
            'verification_status' => 'unverified',
            'captured_at' => now(),
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $evalService = app(NicheEvaluationService::class);
        $evalService->evaluate(
            candidate: $ev,
            snapshot: $snapshot,
            feasibility: 80,
            audienceValidation: 80,
            productionEconomics: 80,
            monetizationDiversification: 80
        );
    }

    public function test_stale_snapshot_detection(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        $freshSnapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'EV',
            'source_niche_name' => 'EV',
            'niche_score' => 78,
            'verification_status' => 'verified',
            'captured_at' => now()->subDays(5),
        ]);

        $staleSnapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'EV',
            'source_niche_name' => 'EV',
            'niche_score' => 78,
            'verification_status' => 'verified',
            'captured_at' => now()->subDays(35),
        ]);

        $this->assertFalse($freshSnapshot->isStale(30));
        $this->assertTrue($staleSnapshot->isStale(30));
    }

    public function test_manual_score_entry_creates_needs_review_status(): void
    {
        Storage::fake('public');
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        $file = UploadedFile::fake()->image('screenshot.png');

        $response = $this->post(route('niches.manual', $ev->id), [
            'niche_score' => 78,
            'cpm_range' => '$6-$18',
            'growth_trend' => 'Rising',
            'equipment_level' => 'Basic',
            'faceless_friendly' => 0,
            'evidence_screenshot' => $file,
            'notes' => 'Manual test snapshot',
        ]);

        $response->assertRedirect();

        $snapshot = NicheScoreSnapshot::where('niche_candidate_id', $ev->id)
            ->where('capture_method', 'manual')
            ->first();

        $this->assertNotNull($snapshot);
        $this->assertEquals(78, $snapshot->niche_score);
        $this->assertEquals('needs_review', $snapshot->verification_status);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $snapshot->evidence_screenshot_path));
    }

    public function test_reviewer_can_verify_snapshot(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        $snapshot = NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'EV',
            'source_niche_name' => 'EV',
            'niche_score' => 78,
            'verification_status' => 'needs_review',
            'captured_at' => now(),
        ]);

        $response = $this->post(route('snapshots.verify', $snapshot->id), [
            'status' => 'verified',
            'notes' => 'Verified by QA test',
        ]);

        $response->assertRedirect();
        $this->assertEquals('verified', $snapshot->fresh()->verification_status);
    }

    public function test_owner_cannot_select_unverified_niche(): void
    {
        $sleep = NicheCandidate::where('slug', 'sleep-content')->first();

        $response = $this->post(route('niches.select', $sleep->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('candidate', $sleep->fresh()->status);
    }

    public function test_owner_can_select_verified_niche(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'EV',
            'source_niche_name' => 'EV',
            'niche_score' => 78,
            'verification_status' => 'verified',
            'captured_at' => now(),
        ]);

        $response = $this->post(route('niches.select', $ev->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('selected', $ev->fresh()->status);
    }

    public function test_audit_log_is_recorded(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();

        NicheScoreSnapshot::create([
            'niche_candidate_id' => $ev->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'EV',
            'source_niche_name' => 'EV',
            'niche_score' => 78,
            'verification_status' => 'verified',
            'captured_at' => now(),
        ]);

        $this->post(route('niches.select', $ev->id));

        $log = AuditLog::where('action', 'select_pilot_niche')->first();
        $this->assertNotNull($log);
        $this->assertEquals('NicheCandidate', $log->entity_type);
        $this->assertEquals($ev->id, $log->entity_id);
    }
}
