<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdvertisementExcludedPattern extends Pivot
{
    protected $table = 'advertisement_excluded_patterns';
    
    public $incrementing = true;
    
    protected $fillable = [
        'advertisement_id',
        'url_pattern_id',
    ];
    
    /**
     * Get the foreign key column name.
     */
    public function getForeignKey()
    {
        return 'advertisement_id';
    }
    
    /**
     * Get the related key column name.
     */
    public function getRelatedKey()
    {
        return 'url_pattern_id';
    }
}

