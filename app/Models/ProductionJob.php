<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductionJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_idea_id',
        'script_id',
        'channel_id',
        'output_profile',
        'render_manifest',
        'status',
        'progress',
        'output_path',
        'thumbnail_path',
        'captions_path',
        'duration_seconds',
        'file_size',
        'render_log_path',
        'qa_report',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'render_manifest' => 'array',
        'qa_report' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function contentIdea(): BelongsTo
    {
        return $this->belongsTo(ContentIdea::class);
    }

    public function script(): BelongsTo
    {
        return $this->belongsTo(Script::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function youtubeUpload(): HasOne
    {
        return $this->hasOne(YoutubeUpload::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}

