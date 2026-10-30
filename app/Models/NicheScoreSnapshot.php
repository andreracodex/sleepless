<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NicheScoreSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'niche_candidate_id',
        'source_name',
        'source_url',
        'matched_query',
        'source_niche_name',
        'niche_score',
        'demand_score',
        'competition_score',
        'monetization_score',
        'cpm_range',
        'growth_trend',
        'faceless_friendly',
        'equipment_level',
        'sub_niches',
        'evidence_screenshot_path',
        'evidence_text',
        'capture_method',
        'parser_version',
        'confidence',
        'verification_status',
        'captured_at',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'sub_niches' => 'array',
        'evidence_text' => 'array',
        'faceless_friendly' => 'boolean',
        'confidence' => 'decimal:2',
        'captured_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function nicheCandidate(): BelongsTo
    {
        return $this->belongsTo(NicheCandidate::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(NicheEvaluation::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isStale(int $days = 30): bool
    {
        return $this->captured_at ? $this->captured_at->diffInDays(now()) > $days : true;
    }
}

