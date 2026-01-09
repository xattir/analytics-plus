<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementDevice extends Model
{
    protected $fillable = [
        'advertisement_id',
        'device_type',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

