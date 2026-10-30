<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContentIdea extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'niche_candidate_id',
        'title_idea',
        'angle',
        'audience_promise',
        'format',
        'target_duration_seconds',
        'keyword_set',
        'novelty_notes',
        'status',
        'priority',
        'created_by',
    ];

    protected $casts = [
        'keyword_set' => 'array',
        'target_duration_seconds' => 'integer',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function nicheCandidate(): BelongsTo
    {
        return $this->belongsTo(NicheCandidate::class);
    }

    public function researchDossier(): HasOne
    {
        return $this->hasOne(ResearchDossier::class);
    }

    public function isLongForm(): bool
    {
        return $this->format === 'long_form';
    }

    public function isShorts(): bool
    {
        return $this->format === 'shorts';
    }
}
