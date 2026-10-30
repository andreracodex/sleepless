<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('niche_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['candidate', 'selected', 'rejected', 'archived'])->default('candidate');
            $table->json('audience_profile')->nullable();
            $table->json('content_pillars')->nullable();
            $table->timestamps();
        });

        Schema::create('niche_score_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niche_candidate_id')->constrained('niche_candidates')->cascadeOnDelete();
            $table->string('source_name')->default('CollabPals YouTube Niche Finder');
            $table->string('source_url')->default('https://www.collabpals.com/tools/youtube-niche-finder');
            $table->string('matched_query');
            $table->string('source_niche_name');
            $table->unsignedInteger('niche_score')->nullable();
            $table->unsignedInteger('demand_score')->nullable();
            $table->unsignedInteger('competition_score')->nullable();
            $table->unsignedInteger('monetization_score')->nullable();
            $table->string('cpm_range')->nullable();
            $table->string('growth_trend')->nullable();
            $table->boolean('faceless_friendly')->nullable();
            $table->string('equipment_level')->nullable();
            $table->json('sub_niches')->nullable();
            $table->string('evidence_screenshot_path')->nullable();
            $table->json('evidence_text')->nullable();
            $table->enum('capture_method', ['browser_automation', 'manual'])->default('browser_automation');
            $table->string('parser_version')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->enum('verification_status', ['verified', 'needs_review', 'unverified'])->default('unverified');
            $table->timestamp('captured_at')->useCurrent();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('niche_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niche_candidate_id')->constrained('niche_candidates')->cascadeOnDelete();
            $table->foreignId('niche_score_snapshot_id')->constrained('niche_score_snapshots')->cascadeOnDelete();
            $table->unsignedInteger('feasibility_score')->default(0);
            $table->unsignedInteger('audience_validation_score')->default(0);
            $table->unsignedInteger('production_economics_score')->default(0);
            $table->unsignedInteger('monetization_diversification_score')->default(0);
            $table->decimal('internal_score', 5, 2)->default(0);
            $table->enum('decision', ['pending', 'recommended', 'selected', 'rejected'])->default('pending');
            $table->text('rationale')->nullable();
            $table->string('evaluated_by')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type')->nullable();
            $table->string('actor_id')->nullable();
            $table->string('action');
            $table->string('entity_type');
            $table->string('entity_id');
            $table->json('before_json')->nullable();
            $table->json('after_json')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('niche_evaluations');
        Schema::dropIfExists('niche_score_snapshots');
        Schema::dropIfExists('niche_candidates');
    }
};
