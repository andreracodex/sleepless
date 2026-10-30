<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\MediaAsset;
use App\Models\ProductionJob;
use App\Models\Script;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class VideoRenderingService
{
    /**
     * Render 1080p video from an approved script and its audio asset
     */
    public function render(Script $script, ?string $profile = 'youtube_landscape_1080p'): ProductionJob
    {
        $idea = $script->contentIdea;
        $channel = $idea->channel;

        $audioAsset = MediaAsset::where('asset_type', 'audio_voiceover')
            ->whereJsonContains('metadata->script_id', $script->id)
            ->latest()
            ->first();

        if (!$audioAsset) {
            throw new RuntimeException("Voiceover audio asset not found for Script #{$script->id}. Please generate audio first.");
        }

        $timestamp = time();
        $outputMp4 = "public/storage/videos/render_idea_{$idea->id}_{$timestamp}.mp4";
        $thumbJpg = "public/storage/videos/thumb_idea_{$idea->id}_{$timestamp}.jpg";

        $fullMp4Path = base_path($outputMp4);
        $fullThumbPath = base_path($thumbJpg);

        $manifestData = [
            'title' => $idea->title_idea,
            'profile' => $profile,
            'audio_path' => base_path("public/{$audioAsset->storage_path}"),
            'scenes' => $script->script_json['scenes'] ?? [],
        ];

        $manifestPath = storage_path("app/render_manifest_{$script->id}_{$timestamp}.json");
        file_put_contents($manifestPath, json_encode($manifestData, JSON_PRETTY_PRINT));

        $job = ProductionJob::create([
            'content_idea_id' => $idea->id,
            'script_id' => $script->id,
            'channel_id' => $channel->id,
            'output_profile' => $profile,
            'render_manifest' => $manifestData,
            'status' => 'rendering',
            'progress' => 20,
            'started_at' => now(),
        ]);

        $renderScript = base_path('scripts/render_video.py');
        $command = "python \"{$renderScript}\" \"{$manifestPath}\" \"{$fullMp4Path}\" \"{$fullThumbPath}\"";

        $result = Process::path(base_path())->timeout(180)->run($command);

        if (file_exists($manifestPath)) {
            unlink($manifestPath);
        }

        if (!$result->successful() || !file_exists($fullMp4Path)) {
            $job->update([
                'status' => 'failed',
                'render_log_path' => $result->errorOutput() . " " . $result->output(),
            ]);
            throw new RuntimeException("Video rendering failed: " . $result->errorOutput());
        }

        $outputStr = $result->output();
        $jsonStart = strpos($outputStr, '{');
        $renderData = [];
        if ($jsonStart !== false) {
            $renderData = json_decode(substr($outputStr, $jsonStart), true) ?? [];
        }

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'output_path' => str_replace('public/', '', $outputMp4),
            'thumbnail_path' => str_replace('public/', '', $thumbJpg),
            'captions_path' => $audioAsset->metadata['srt_path'] ?? null,
            'duration_seconds' => $renderData['duration_seconds'] ?? null,
            'file_size' => $renderData['file_size'] ?? filesize($fullMp4Path),
            'qa_report' => $renderData['qa_report'] ?? [],
            'completed_at' => now(),
        ]);

        AuditLog::log(
            action: 'render_production_video',
            entityType: 'ProductionJob',
            entityId: $job->id,
            after: [
                'output_path' => $job->output_path,
                'qa_passed' => $job->qa_report['qa_passed'] ?? false,
            ],
            actorType: 'renderer_worker'
        );

        return $job;
    }
}
