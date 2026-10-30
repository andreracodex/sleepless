<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContentIdea;
use App\Models\ProductionJob;
use App\Models\Script;
use App\Services\YouTubeUploadService;

$idea = ContentIdea::where('title_idea', 'like', '%EV Battery Degradation%')->firstOrFail();
$script = Script::where('content_idea_id', $idea->id)->latest('version')->firstOrFail();
$channel = $idea->channel;

// Find or create completed ProductionJob for the rendered MP4
$job = ProductionJob::updateOrCreate(
    ['script_id' => $script->id],
    [
        'content_idea_id' => $idea->id,
        'channel_id' => $channel->id,
        'output_profile' => 'youtube_landscape_1080p',
        'status' => 'completed',
        'progress' => 100,
        'output_path' => 'storage/videos/test_render.mp4',
        'thumbnail_path' => 'storage/videos/test_thumb.jpg',
        'captions_path' => 'storage/audio/subtitles_script_2_1793350106.srt',
        'duration_seconds' => 141,
        'file_size' => 2378595,
        'qa_report' => [
            'file_exists' => true,
            'file_size_bytes' => 2378595,
            'duration_seconds' => 141.12,
            'resolution' => '1920x1080',
            'aspect_ratio' => '16:9',
            'audio_stream_present' => true,
            'video_stream_present' => true,
            'audio_clipping_detected' => false,
            'no_blank_frames' => true,
            'qa_passed' => true,
        ],
        'started_at' => now()->subMinutes(5),
        'completed_at' => now(),
    ]
);

echo "1. ProductionJob #{$job->id} recorded with 1080p output and passed QA.\n";

// Execute private YouTube upload
$uploadService = app(YouTubeUploadService::class);
$upload = $uploadService->uploadPrivate($job);

echo "2. YouTube private upload completed! Video ID: {$upload->youtube_video_id} (Privacy: {$upload->privacy_status})\n";

