<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdvertisementUrlPattern extends Pivot
{
    protected $table = 'advertisement_url_patterns';
    
    public $incrementing = true;
    
    protected $fillable = [
        'advertisement_id',
        'url_pattern_id',
    ];
    
    /**
     * Get the key for the related model.
     */
    public function getRelatedKey()
    {
        return 'url_pattern_id';
    }
    
    /**
     * Get the key for the parent model.
     */
    public function getForeignKey()
    {
        return 'advertisement_id';
    }
}

