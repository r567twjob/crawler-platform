<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleReviews extends Model
{
    //
    protected $fillable = [
        'place_id',
        'name',
        'relativePublishTimeDescription',
        'rating',
        'text',
        'authorName'
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
