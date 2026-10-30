<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchDossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_idea_id',
        'research_summary',
        'source_ledger',
        'claim_ledger',
        'risk_flags',
        'reviewer_id',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'source_ledger' => 'array',
        'claim_ledger' => 'array',
        'risk_flags' => 'array',
        'approved_at' => 'datetime',
    ];

    public function contentIdea(): BelongsTo
    {
        return $this->belongsTo(ContentIdea::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}

