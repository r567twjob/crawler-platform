<?php

namespace App\Http\Controllers;

use App\Jobs\NearbySearchJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ProcessController extends Controller
{

    public function startNearbySearch(Request $request)
    {
        $district = $request->input('district');
        $districts = json_decode(
            file_get_contents(storage_path('app/tainan_districts.json')),
            true
        );

        $latMin = $districts[$district]['lat_min'];
        $latMax = $districts[$district]['lat_max'];
        $lngMin = $districts[$district]['lng_min'];
        $lngMax = $districts[$district]['lng_max'];
        $step = 0.005;

        $grid = [];
        for ($lat = $latMin; $lat <= $latMax; $lat += $step) {
            for ($lng = $lngMin; $lng <= $lngMax; $lng += $step) {
                $grid[] = [round($lat, 6), round($lng, 6)];
            }
        }

        Cache::put($district . '_nearby_request_count', 0);
        Cache::put($district . '_nearby_progress', 0);
        Cache::put($district . '_nearby_total', count($grid));

        foreach ($grid as [$lat, $lng]) {
            dispatch(new \App\Jobs\NearbySearchJob($lat, $lng, $district));
        }

        $districts[$district]["processed"] = true;
        file_put_contents(
            storage_path('app/tainan_districts.json'),
            json_encode($districts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return redirect()->route('list');
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
