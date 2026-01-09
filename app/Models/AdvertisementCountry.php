<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementCountry extends Model
{
    protected $fillable = [
        'advertisement_id',
        'country_code',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

