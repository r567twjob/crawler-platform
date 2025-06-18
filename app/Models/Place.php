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

    public function getTypesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTypesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['types'] = implode(',', $value);
        } else {
            $this->attributes['types'] = $value;
        }
    }

    public function reviews()
    {
        return $this->hasMany(GoogleReviews::class);
    }
}
