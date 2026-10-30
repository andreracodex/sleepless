<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ContentIdea;
use App\Models\NicheCandidate;
use App\Models\ResearchDossier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NicheCandidateSeeder::class);
        $this->seed(\Database\Seeders\ChannelAndIdeaSeeder::class);
    }

    public function test_idea_board_renders_successfully(): void
    {
        $response = $this->get('/ideas');
        $response->assertStatus(200);
        $response->assertSee('VoltWise: The EV Explainer');
        $response->assertSee('EV Battery Degradation');
        $response->assertSee('Long-form');
        $response->assertSee('Shorts');
    }

    public function test_filter_ideas_by_format(): void
    {
        $responseLong = $this->get('/ideas?format=long_form');
        $responseLong->assertStatus(200);
        $responseLong->assertSee('EV Battery Degradation');

        $responseShorts = $this->get('/ideas?format=shorts');
        $responseShorts->assertStatus(200);
        $responseShorts->assertSee('The 80% Charging Rule Explained');
    }

    public function test_create_new_content_idea(): void
    {
        $channel = Channel::firstOrFail();

        $response = $this->post(route('ideas.store'), [
            'channel_id' => $channel->id,
            'title_idea' => 'Solid-State Battery EV in 2026: Myth vs Reality',
            'angle' => 'Comparing lab prototypes with mass manufacturing hurdles.',
            'audience_promise' => 'Understand the true commercial timeline for solid-state batteries.',
            'format' => 'long_form',
            'target_duration_seconds' => 720,
            'keywords' => 'solid state battery, ev technology, quantumscape',
            'priority' => 'high',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $idea = ContentIdea::where('title_idea', 'Solid-State Battery EV in 2026: Myth vs Reality')->first();
        $this->assertNotNull($idea);
        $this->assertEquals('accepted', $idea->status);
        $this->assertEquals('high', $idea->priority);
    }

    public function test_view_research_dossier_with_claim_ledger(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();

        $response = $this->get(route('ideas.dossier', $idea->id));
        $response->assertStatus(200);
        $response->assertSee('PRD FR-006', false);
        $response->assertSee('Claim Ledger');
        $response->assertSee('Source Ledger');
        $response->assertSee('Geotab');
        $response->assertSee('1.8% and 2.3%');
    }

    public function test_approve_research_dossier_promotes_idea_to_scripting(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;

        $response = $this->post(route('dossier.review', $dossier->id), [
            'action' => 'approve',
            'notes' => 'All 4 claims verified against primary fleet studies.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('approved', $dossier->fresh()->status);
        $this->assertEquals('scripting', $idea->fresh()->status);

        $log = AuditLog::where('action', 'review_research_dossier')->first();
        $this->assertNotNull($log);
    }

    public function test_idea_board_dynamically_switches_to_selected_niche(): void
    {
        $sleep = NicheCandidate::where('slug', 'sleep-content')->firstOrFail();

        \App\Models\NicheScoreSnapshot::create([
            'niche_candidate_id' => $sleep->id,
            'source_name' => 'CollabPals',
            'source_url' => 'https://collabpals.com',
            'matched_query' => 'Sleep Content',
            'source_niche_name' => 'Sleep Content',
            'niche_score' => 72,
            'verification_status' => 'verified',
            'captured_at' => now(),
        ]);

        // Select Sleep Content in Niche Lab
        $selectResponse = $this->post(route('niches.select', $sleep->id));
        $selectResponse->assertRedirect();

        // Visit Idea Board
        $response = $this->get(route('ideas.index'));
        $response->assertStatus(200);
        $response->assertSee('Sleep Content');
        $response->assertSee('Terhubung ke Niche:');
        $response->assertSee('8 Hours Deep Ocean Rain');
    }
}
