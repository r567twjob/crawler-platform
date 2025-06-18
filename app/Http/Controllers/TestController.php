<?php

namespace App\Http\Controllers;

use App\Jobs\AddPlaceJob;
use App\Models\Grid;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function process_grid()
    {
        $grid = Grid::find(1);
        AddPlaceJob::dispatch($grid, 'google')->onQueue('default');
    }
}
