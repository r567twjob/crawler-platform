<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    //
    protected $fillable = ['name'];

    public function places()
    {
        return $this->belongsToMany(Place::class, 'map_place', 'map_id', 'place_id');
    }
}
