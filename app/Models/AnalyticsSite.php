<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSite extends Model
{
    protected $table = 'analytics_sites';
    
    protected $fillable = [
        'domain',
        'title',
        'user_id',
        'order',
        'site_key',
    ];
    
    protected $casts = [
        'order' => 'integer',
    ];
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'site_key';
    }
    
    /**
     * Get all sessions for this site
     */
    public function sessions()
    {
        return $this->hasMany(AnalyticsSession::class, 'site_id');
    }
    
    /**
     * Get the owner (user who created the site)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get all users who have access to this site
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'analytics_site_users', 'site_id', 'user_id')
            ->withTimestamps();
    }
    
    /**
     * Get all invitations for this site
     */
    public function invitations()
    {
        return $this->hasMany(AnalyticsSiteInvitation::class, 'site_id');
    }
    
    /**
     * Get all URL patterns for this site
     */
    public function urlPatterns()
    {
        return $this->hasMany(AnalyticsUrlPattern::class, 'site_id');
    }
    
    /**
     * Check if a user can access this site
     */
    public function canAccess($userId)
    {
        // Owner can always access
        if ($this->user_id == $userId) {
            return true;
        }
        
        // Check if user is a member
        return $this->users()->where('user_id', $userId)->exists();
    }
}
