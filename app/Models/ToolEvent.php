<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'tool',
        'action',
        'status',
        'metadata',
        'visitor_hash',
        'ip_hash',
        'user_agent_hash',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
