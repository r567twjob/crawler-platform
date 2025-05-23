<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'lat_min', 'lat_max', 'lng_min', 'lng_max', 'processed'];
    //

    public function grids()
    {
        return $this->hasMany(Grid::class);
    }
}
