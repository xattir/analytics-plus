<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AnalyticsSiteInvitation extends Model
{
    protected $fillable = [
        'site_id',
        'invited_by',
        'email',
        'token',
        'status',
        'expires_at',
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    
    /**
     * Generate a unique token for the invitation
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }
    
    /**
     * Check if the invitation has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    /**
     * Get the site that this invitation is for
     */
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }
    
    /**
     * Get the user who sent the invitation
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
