<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'niche_candidate_id',
        'youtube_channel_id',
        'channel_name',
        'handle',
        'primary_language',
        'target_region',
        'timezone',
        'brand_config',
        'oauth_credential_encrypted',
        'default_visibility',
        'status',
    ];

    protected $casts = [
        'brand_config' => 'array',
    ];

    public function nicheCandidate(): BelongsTo
    {
        return $this->belongsTo(NicheCandidate::class);
    }

    public function contentIdeas(): HasMany
    {
        return $this->hasMany(ContentIdea::class)->orderByDesc('created_at');
    }
}

