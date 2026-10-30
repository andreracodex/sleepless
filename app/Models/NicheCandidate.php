<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NicheCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'status',
        'audience_profile',
        'content_pillars',
    ];

    protected $casts = [
        'audience_profile' => 'array',
        'content_pillars' => 'array',
    ];

    public function scoreSnapshots(): HasMany
    {
        return $this->hasMany(NicheScoreSnapshot::class)->orderByDesc('captured_at');
    }

    public function latestVerifiedSnapshot(): HasOne
    {
        return $this->hasOne(NicheScoreSnapshot::class)
            ->where('verification_status', 'verified')
            ->latestOfMany('captured_at');
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(NicheScoreSnapshot::class)->latestOfMany('captured_at');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(NicheEvaluation::class)->orderByDesc('created_at');
    }

    public function latestEvaluation(): HasOne
    {
        return $this->hasOne(NicheEvaluation::class)->latestOfMany('created_at');
    }
}
