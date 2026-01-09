<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementSubdomain extends Model
{
    protected $fillable = [
        'advertisement_id',
        'subdomain',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

