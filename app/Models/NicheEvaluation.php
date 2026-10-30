<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NicheEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'niche_candidate_id',
        'niche_score_snapshot_id',
        'feasibility_score',
        'audience_validation_score',
        'production_economics_score',
        'monetization_diversification_score',
        'internal_score',
        'decision',
        'rationale',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'internal_score' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    public function nicheCandidate(): BelongsTo
    {
        return $this->belongsTo(NicheCandidate::class);
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(NicheScoreSnapshot::class, 'niche_score_snapshot_id');
    }

    /**
     * Calculate internal score according to PRD section 3.4
     * internal_score = 0.40 * niche_score + 0.20 * feasibility + 0.20 * audience + 0.10 * economics + 0.10 * diversification
     */
    public static function calculateInternalScore(
        float $nicheScore,
        float $feasibility,
        float $audience,
        float $economics,
        float $diversification
    ): float {
        return round(
            (0.40 * $nicheScore) +
            (0.20 * $feasibility) +
            (0.20 * $audience) +
            (0.10 * $economics) +
            (0.10 * $diversification),
            2
        );
    }
}

