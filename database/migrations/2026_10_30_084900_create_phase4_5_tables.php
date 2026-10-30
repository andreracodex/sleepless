<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_idea_id')->constrained('content_ideas')->cascadeOnDelete();
            $table->foreignId('script_id')->constrained('scripts')->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->enum('output_profile', ['youtube_landscape_1080p', 'shorts_vertical_1080p'])->default('youtube_landscape_1080p');
            $table->json('render_manifest')->nullable();
            $table->enum('status', ['pending', 'rendering', 'completed', 'failed'])->default('pending');
            $table->unsignedInteger('progress')->default(0);
            $table->string('output_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('captions_path')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('render_log_path')->nullable();
            $table->json('qa_report')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('youtube_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_job_id')->constrained('production_jobs')->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->string('youtube_video_id')->nullable();
            $table->string('idempotency_key')->unique();
            $table->enum('privacy_status', ['private', 'unlisted', 'public'])->default('private');
            $table->enum('upload_session_status', ['pending', 'uploading', 'uploaded', 'failed'])->default('pending');
            $table->json('upload_response')->nullable();
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->string('category_id')->default('28'); // Science & Technology
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_uploads');
        Schema::dropIfExists('production_jobs');
    }
};

