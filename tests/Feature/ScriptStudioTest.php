<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ContentIdea;
use App\Models\MediaAsset;
use App\Models\ResearchDossier;
use App\Models\Script;
use App\Services\ScriptGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScriptStudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NicheCandidateSeeder::class);
        $this->seed(\Database\Seeders\ChannelAndIdeaSeeder::class);
    }

    public function test_script_page_renders_successfully(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();

        $response = $this->get(route('scripts.show', $idea->id));
        $response->assertStatus(200);
        $response->assertSee('Phase 3: Script', false);
    }

    public function test_script_cannot_be_generated_if_dossier_is_not_approved(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'draft']);

        $response = $this->post(route('scripts.generate', $idea->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('scripts', ['content_idea_id' => $idea->id]);
    }

    public function test_script_generates_with_claim_references_when_dossier_approved(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'approved', 'approved_at' => now()]);

        $response = $this->post(route('scripts.generate', $idea->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $script = Script::where('content_idea_id', $idea->id)->first();
        $this->assertNotNull($script);
        $this->assertGreaterThan(100, $script->word_count);
        $this->assertEquals('needs_review', $script->status);

        // Verify scene 2 has CLM-01
        $scenes = $script->script_json['scenes'];
        $this->assertCount(6, $scenes);
        $this->assertContains('CLM-01', $scenes[1]['claim_references']);
    }

    public function test_script_generates_in_indonesian_with_indonesian_voice_selection(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'approved', 'approved_at' => now()]);

        $response = $this->post(route('scripts.generate', $idea->id), [
            'language' => 'id',
            'voice' => 'id-ID-ArdiNeural',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $script = Script::where('content_idea_id', $idea->id)->latest('version')->first();
        $this->assertNotNull($script);
        $this->assertEquals('id', $script->language);
        $this->assertEquals('id-ID-ArdiNeural', $script->script_json['selected_voice']);
        $this->assertStringContainsString('baterai', strtolower($script->script_json['full_narration']));
    }

    public function test_script_generates_in_english_with_english_voice_selection(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'approved', 'approved_at' => now()]);

        $response = $this->post(route('scripts.generate', $idea->id), [
            'language' => 'en',
            'voice' => 'en-US-JennyNeural',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $script = Script::where('content_idea_id', $idea->id)->latest('version')->first();
        $this->assertNotNull($script);
        $this->assertEquals('en', $script->language);
        $this->assertEquals('en-US-JennyNeural', $script->script_json['selected_voice']);
        $this->assertStringContainsString('battery', strtolower($script->script_json['full_narration']));
    }

    public function test_script_generates_for_shorts_format(): void
    {
        $shortsIdea = ContentIdea::where('format', 'shorts')->first();
        if (!$shortsIdea) {
            $shortsIdea = ContentIdea::create([
                'channel_id' => Channel::first()->id,
                'title_idea' => 'EV Fast Charging Secret in 45 Seconds',
                'angle' => 'Why fast charging curves flatten at 80%',
                'audience_promise' => 'Save time charging on road trips',
                'format' => 'shorts',
                'target_duration_seconds' => 45,
                'status' => 'accepted',
                'priority' => 'high',
            ]);
        }

        $dossier = ResearchDossier::create([
            'content_idea_id' => $shortsIdea->id,
            'research_summary' => 'Quick telemetry notes on DC fast charging curves.',
            'source_ledger' => [
                ['source_id' => 'SRC-01', 'title' => 'Fast Charging Curve Dataset', 'publisher' => 'Lab'],
            ],
            'claim_ledger' => [
                [
                    'claim_id' => 'CLM-01',
                    'claim' => 'DC fast charging speed drops by 60% above 80% SoC.',
                    'source_ref' => 'SRC-01',
                    'confidence_score' => 95,
                    'is_verified' => true,
                ],
                [
                    'claim_id' => 'CLM-02',
                    'claim' => 'Leaving at 80% saves 25 minutes of wait time on highways.',
                    'source_ref' => 'SRC-02',
                    'confidence_score' => 90,
                    'is_verified' => true,
                ],
            ],
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $service = app(ScriptGenerationService::class);
        $script = $service->generateScript($shortsIdea, $dossier, 'id');

        $this->assertNotNull($script);
        $this->assertCount(3, $script->script_json['scenes']);
        $this->assertContains('CLM-01', $script->script_json['scenes'][1]['claim_references']);
    }

    public function test_creator_can_generate_multiple_language_versions_for_same_idea(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'approved', 'approved_at' => now()]);

        // Version 1 in Indonesian
        $this->post(route('scripts.generate', $idea->id), ['language' => 'id']);
        $v1 = Script::where('content_idea_id', $idea->id)->where('version', 1)->first();
        $this->assertNotNull($v1);
        $this->assertEquals('id', $v1->language);

        // Version 2 in English
        $this->post(route('scripts.generate', $idea->id), ['language' => 'en']);
        $v2 = Script::where('content_idea_id', $idea->id)->where('version', 2)->first();
        $this->assertNotNull($v2);
        $this->assertEquals('en', $v2->language);
        $this->assertEquals(2, $v2->version);
    }

    public function test_reviewer_can_approve_script(): void
    {
        $idea = ContentIdea::whereHas('researchDossier')->firstOrFail();
        $dossier = $idea->researchDossier;
        $dossier->update(['status' => 'approved']);

        $service = app(ScriptGenerationService::class);
        $script = $service->generateScript($idea, $dossier);

        $response = $this->post(route('scripts.approve', $script->id), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $this->assertEquals('approved', $script->fresh()->status);
        $this->assertEquals('production', $idea->fresh()->status);
    }
}
