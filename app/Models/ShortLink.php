<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    /** @use HasFactory<\Database\Factories\ShortLinkFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'destination_url',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_clicked_at' => 'datetime',
        ];
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ShortLinkClick::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
