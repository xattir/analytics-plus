<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSite extends Model
{
    protected $table = 'analytics_sites';
    
    protected $fillable = [
        'user_id',
        'site_key',
        'domain',
        'title',
        'order',
    ];
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'site_key';
    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'analytics_site_users', 'site_id', 'user_id')
            ->withTimestamps();
    }
    
    public function invitations()
    {
        return $this->hasMany(AnalyticsSiteInvitation::class, 'site_id');
    }
    
    public function sessions()
    {
        return $this->hasMany(AnalyticsSession::class, 'site_id');
    }
    
    public function paths()
    {
        return $this->hasMany(AnalyticsSessionPath::class, 'site_id');
    }
    
    /**
     * Check if user can access this site (owner or member)
     */
    public function canAccess($userId)
    {
        return $this->user_id == $userId || $this->users()->where('user_id', $userId)->exists();
    }
    
    /**
     * Get all users who can access this site (owner + members)
     */
    public function getAllUsers()
    {
        $users = collect([$this->owner]);
        $users = $users->merge($this->users);
        return $users->unique('id');
    }
}
