<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'storage_path',
        'source_provider',
        'source_url',
        'author',
        'license_name',
        'license_url',
        'attribution_text',
        'commercial_use_allowed',
        'monetization_allowed',
        'proof_path',
        'acquired_at',
        'expiry_at',
        'rights_status',
        'metadata',
    ];

    protected $casts = [
        'commercial_use_allowed' => 'boolean',
        'monetization_allowed' => 'boolean',
        'acquired_at' => 'datetime',
        'expiry_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function isCleared(): bool
    {
        return $this->rights_status === 'approved' && $this->commercial_use_allowed && $this->monetization_allowed;
    }
}
