<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ContentIdea;
use App\Models\ProductionJob;
use App\Models\ResearchDossier;
use App\Models\Script;
use App\Models\YoutubeUpload;
use App\Services\YouTubeUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionStudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NicheCandidateSeeder::class);
        $this->seed(\Database\Seeders\ChannelAndIdeaSeeder::class);
    }

    public function test_production_studio_page_renders_successfully(): void
    {
        $response = $this->get('/production');
        $response->assertStatus(200);
        $response->assertSee('Production Jobs & YouTube Publishing Center', false);
    }

    public function test_render_fails_if_script_is_not_approved(): void
    {
        $idea = ContentIdea::firstOrFail();
        $dossier = ResearchDossier::firstOrFail();

        $script = Script::create([
            'content_idea_id' => $idea->id,
            'research_dossier_id' => $dossier->id,
            'version' => 1,
            'language' => 'en',
            'script_json' => ['scenes' => []],
            'word_count' => 50,
            'estimated_duration_seconds' => 30,
            'status' => 'needs_review',
        ]);

        $response = $this->post(route('production.render', $script->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('production_jobs', ['script_id' => $script->id]);
    }

    public function test_youtube_upload_enforces_private_status_and_idempotency(): void
    {
        $idea = ContentIdea::firstOrFail();
        $channel = Channel::firstOrFail();
        $dossier = ResearchDossier::firstOrFail();

        $script = Script::create([
            'content_idea_id' => $idea->id,
            'research_dossier_id' => $dossier->id,
            'version' => 1,
            'language' => 'en',
            'script_json' => ['scenes' => []],
            'word_count' => 100,
            'estimated_duration_seconds' => 60,
            'status' => 'approved',
        ]);

        $job = ProductionJob::create([
            'content_idea_id' => $idea->id,
            'script_id' => $script->id,
            'channel_id' => $channel->id,
            'output_profile' => 'youtube_landscape_1080p',
            'status' => 'completed',
            'progress' => 100,
            'output_path' => 'storage/videos/test.mp4',
            'duration_seconds' => 60,
            'file_size' => 1048576,
            'qa_report' => ['qa_passed' => true],
        ]);

        $uploadService = app(YouTubeUploadService::class);
        $upload1 = $uploadService->uploadPrivate($job);

        $this->assertEquals('private', $upload1->privacy_status);
        $this->assertEquals('uploaded', $upload1->upload_session_status);
        $this->assertNotNull($upload1->youtube_video_id);

        // Test idempotency: calling again does not create a duplicate record
        $upload2 = $uploadService->uploadPrivate($job);
        $this->assertEquals($upload1->id, $upload2->id);
        $this->assertEquals(1, YoutubeUpload::where('production_job_id', $job->id)->count());
    }

    public function test_owner_can_approve_publishing_visibility(): void
    {
        $idea = ContentIdea::firstOrFail();
        $channel = Channel::firstOrFail();
        $dossier = ResearchDossier::firstOrFail();

        $script = Script::create([
            'content_idea_id' => $idea->id,
            'research_dossier_id' => $dossier->id,
            'version' => 1,
            'script_json' => ['scenes' => []],
            'status' => 'approved',
        ]);

        $job = ProductionJob::create([
            'content_idea_id' => $idea->id,
            'script_id' => $script->id,
            'channel_id' => $channel->id,
            'status' => 'completed',
            'qa_report' => ['qa_passed' => true],
        ]);

        $upload = YoutubeUpload::create([
            'production_job_id' => $job->id,
            'channel_id' => $channel->id,
            'idempotency_key' => 'test_idempotent_key',
            'privacy_status' => 'private',
            'upload_session_status' => 'uploaded',
            'title' => 'Test Video',
            'description' => 'Test Description',
        ]);

        $response = $this->post(route('production.publish', $upload->id), [
            'privacy_status' => 'public',
        ]);

        $response->assertRedirect();
        $this->assertEquals('public', $upload->fresh()->privacy_status);
        $this->assertNotNull($upload->fresh()->published_at);

        $log = AuditLog::where('action', 'owner_approve_youtube_publish')->first();
        $this->assertNotNull($log);
    }
}
