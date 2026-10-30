<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YoutubeUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_job_id',
        'channel_id',
        'youtube_video_id',
        'idempotency_key',
        'privacy_status',
        'upload_session_status',
        'upload_response',
        'title',
        'description',
        'tags',
        'category_id',
        'scheduled_at',
        'uploaded_at',
        'published_at',
        'error_code',
        'error_message',
    ];

    protected $casts = [
        'upload_response' => 'array',
        'tags' => 'array',
        'scheduled_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function productionJob(): BelongsTo
    {
        return $this->belongsTo(ProductionJob::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
