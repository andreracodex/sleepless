<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContentIdea;
use App\Models\ResearchDossier;
use App\Services\ScriptGenerationService;

$idea = ContentIdea::where('title_idea', 'like', '%EV Battery Degradation%')->firstOrFail();
$dossier = $idea->researchDossier;

if ($dossier) {
    // 1. Approve dossier
    $dossier->update([
        'status' => 'approved',
        'approved_at' => now(),
        'reviewer_id' => 'Owner Admin',
    ]);
    echo "1. Dossier #{$dossier->id} approved.\n";

    // 2. Generate script
    $scriptService = app(ScriptGenerationService::class);
    $script = $scriptService->generateScript($idea, $dossier);
    $script->update([
        'status' => 'approved',
        'approved_by' => 'Owner Admin',
        'approved_at' => now(),
    ]);
    echo "2. Script #{$script->id} generated and approved ({$script->word_count} words, {$script->estimated_duration_seconds}s).\n";

    // 3. Generate voiceover audio
    $audioAsset = $scriptService->generateVoiceoverAudio($script, 'en-US-ChristopherNeural');
    echo "3. Voiceover audio generated: {$audioAsset->storage_path} (Size: {$audioAsset->metadata['file_size']} bytes).\n";
}
