<?php

namespace App\Http\Controllers;

use App\Jobs\NearbySearchJob;
use App\Models\District;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessController extends Controller
{

    public function startNearbySearch(Request $request)
    {
        Log::info('Start nearby search', [
            'request' => $request->all(),
            'district' => $request->input('district')
        ]);

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

        Cache::put($district->name . '_nearby_request_count', 0);
        Cache::put($district->name . '_nearby_progress', 0);
        Cache::put($district->name . '_nearby_total', count($grid));

        foreach ($grid as [$lat, $lng]) {
            $district->grids()->create([
                'lat' => $lat,
                'lng' => $lng
            ]);
            dispatch(new \App\Jobs\NearbySearchJob($lat, $lng, $district));
        }

        $district->processed = true;
        $district->save();

        return response()->json(["message" => "Success"]);
    }

    public function getProgress()
    {
        $district = request()->input('district');

        return [
            'done' => Cache::get($district . '_nearby_progress', 0),
            'total' => Cache::get($district . '_nearby_total', 0),
            'request_count' => Cache::get($district . '_nearby_request_count', 0)
        ];
    }
}
