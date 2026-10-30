<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Script extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_idea_id',
        'research_dossier_id',
        'version',
        'language',
        'script_json',
        'word_count',
        'estimated_duration_seconds',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'script_json' => 'array',
        'approved_at' => 'datetime',
    ];

    public function contentIdea(): BelongsTo
    {
        return $this->belongsTo(ContentIdea::class);
    }

    public function researchDossier(): BelongsTo
    {
        return $this->belongsTo(ResearchDossier::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
