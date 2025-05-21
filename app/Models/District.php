<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name', 'lat_min', 'lat_max', 'lng_min', 'lng_max', 'processed'];
    //
}
