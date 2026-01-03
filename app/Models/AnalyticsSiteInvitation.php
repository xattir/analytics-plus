<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnalyticsSiteInvitation extends Model
{
    protected $table = 'analytics_site_invitations';
    
    protected $fillable = [
        'site_id',
        'invited_by',
        'email',
        'token',
        'status',
        'expires_at',
        'accepted_at',
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];
    
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }
    
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
    
    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
    
    /**
     * Generate a unique invitation token
     */
    public static function generateToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());
        
        return $token;
    }
    
    /**
     * Check if invitation is expired
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }
    
    /**
     * Check if invitation is valid (not expired and pending)
     */
    public function isValid()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}
