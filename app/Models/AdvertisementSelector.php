<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementSelector extends Model
{
    protected $fillable = [
        'advertisement_id',
        'selector',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}

