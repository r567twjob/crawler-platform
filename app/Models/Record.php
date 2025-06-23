<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    //

    public function places()
    {
        return $this->belongsToMany(Place::class, 'record_place', 'record_id', 'place_id');
    }
}
