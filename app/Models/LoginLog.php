<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'logged_in_at'];

    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a short, readable browser/device string from user_agent.
     */
    public function getDeviceAttribute(): string
    {
        $ua = $this->user_agent ?? '';

        if (str_contains($ua, 'iPhone')) return 'iPhone';
        if (str_contains($ua, 'iPad')) return 'iPad';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Macintosh')) return 'Mac';
        if (str_contains($ua, 'Linux')) return 'Linux';

        return 'Unknown';
    }
}
