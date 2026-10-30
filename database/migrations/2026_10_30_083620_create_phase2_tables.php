<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niche_candidate_id')->constrained('niche_candidates')->cascadeOnDelete();
            $table->string('youtube_channel_id')->nullable();
            $table->string('channel_name');
            $table->string('handle')->nullable();
            $table->string('primary_language')->default('en');
            $table->string('target_region')->default('US');
            $table->string('timezone')->default('America/New_York');
            $table->json('brand_config')->nullable();
            $table->text('oauth_credential_encrypted')->nullable();
            $table->string('default_visibility')->default('private');
            $table->enum('status', ['draft', 'active', 'paused'])->default('active');
            $table->timestamps();
        });

        Schema::create('content_ideas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->foreignId('niche_candidate_id')->constrained('niche_candidates')->cascadeOnDelete();
            $table->string('title_idea');
            $table->text('angle');
            $table->text('audience_promise');
            $table->enum('format', ['long_form', 'shorts'])->default('long_form');
            $table->unsignedInteger('target_duration_seconds')->default(600); // 10 minutes default
            $table->json('keyword_set')->nullable();
            $table->text('novelty_notes')->nullable();
            $table->enum('status', [
                'backlog',
                'accepted',
                'rejected',
                'parked',
                'researching',
                'scripting',
                'production',
                'published'
            ])->default('backlog');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('created_by')->nullable()->default('AI Content Researcher');
            $table->timestamps();
        });

        Schema::create('research_dossiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_idea_id')->constrained('content_ideas')->cascadeOnDelete();
            $table->text('research_summary');
            $table->json('source_ledger');
            $table->json('claim_ledger');
            $table->json('risk_flags')->nullable();
            $table->string('reviewer_id')->nullable();
            $table->enum('status', ['draft', 'researching', 'needs_review', 'approved', 'rejected'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_dossiers');
        Schema::dropIfExists('content_ideas');
        Schema::dropIfExists('channels');
    }
};

