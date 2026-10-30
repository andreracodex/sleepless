<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_idea_id')->constrained('content_ideas')->cascadeOnDelete();
            $table->foreignId('research_dossier_id')->constrained('research_dossiers')->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('language')->default('en');
            $table->json('script_json');
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('estimated_duration_seconds')->default(0);
            $table->enum('status', ['draft', 'needs_review', 'approved', 'rejected'])->default('draft');
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->enum('asset_type', ['video', 'image', 'audio_voiceover', 'audio_bgm', 'audio_sfx']);
            $table->string('storage_path');
            $table->string('source_provider')->default('local');
            $table->string('source_url')->nullable();
            $table->string('author')->nullable();
            $table->string('license_name')->default('Commercial Rights Cleared / Original');
            $table->string('license_url')->nullable();
            $table->text('attribution_text')->nullable();
            $table->boolean('commercial_use_allowed')->default(true);
            $table->boolean('monetization_allowed')->default(true);
            $table->string('proof_path')->nullable();
            $table->timestamp('acquired_at')->useCurrent();
            $table->timestamp('expiry_at')->nullable();
            $table->enum('rights_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('scripts');
    }
};

