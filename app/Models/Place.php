<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    //
    protected $fillable = [
        'resource',
        'unique_id',
        'formatted_address',
        'name',
        'types',
        'rating',
        'user_rating_count',
        'google_maps_uri'
    ];
}
