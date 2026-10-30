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
        $response->assertSee('Script & Audio Studio', false);
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
