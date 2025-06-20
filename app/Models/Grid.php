<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grid extends Model
{
    protected $fillable = [
        'district_id',
        'name',
        'lat',
        'lng',
    ];

    //
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
