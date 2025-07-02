<?php

namespace App\Http\Controllers;

use App\Jobs\CreatePlaceFromJsonJob;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class JSONController extends Controller
{

    public function processJSON(Request $request)
    {
        $files = Storage::disk('local')->allFiles('json/');


        $places = [];

        foreach ($files as $file) {
            CreatePlaceFromJsonJob::dispatch($file)->onQueue('default');
        }

        return response()->json(['status' => 'success']);
    }
}
