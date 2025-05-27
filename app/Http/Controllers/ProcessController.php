<?php

namespace App\Http\Controllers;

use App\Jobs\NearbySearchJob;
use App\Models\District;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ProcessController extends Controller
{

    public function startNearbySearch(Request $request)
    {
        $district = District::find($request->input('district'));

        if ($district->processed) {
            return response()->json(['message' => 'This district has already been processed.'], 400);
        }

        $latMin = $district->lat_min;
        $latMax = $district->lat_max;
        $lngMin = $district->lng_min;
        $lngMax = $district->lng_max;
        $step = 0.005;

        $grid = [];
        for ($lat = $latMin; $lat <= $latMax; $lat += $step) {
            for ($lng = $lngMin; $lng <= $lngMax; $lng += $step) {
                $grid[] = [round($lat, 6), round($lng, 6)];
            }
        }

        Cache::put($district->id . '_nearby_progress', 0);

        foreach ($grid as [$lat, $lng]) {
            $grid = $district->grids()->create([
                'lat' => $lat,
                'lng' => $lng
            ]);
            NearbySearchJob::dispatch($lat, $lng, $grid)->onQueue('nearby_search');
        }

        $district->processed = true;
        $district->save();

        return response()->json(["message" => "Success"]);
    }
}
