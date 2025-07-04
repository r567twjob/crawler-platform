<?php

namespace App\Http\Controllers;

use App\Jobs\CreateGridJob;
use App\Jobs\GoogleNearbySearchJob;
use App\Models\District;
use App\Models\Grid;
use App\Models\Record;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ProcessController extends Controller
{

    public function startNearbySearch(Request $request)
    {
        // 檢查是否有 Total Request Cache
        // $totalRequestCache = Cache::get('today_request_count', 0);
        // if ($totalRequestCache >= config('services.google_places.max_requests', 10)) {
        //     return response()->json(['message' => '今日已達請求上限'], 429);
        // }

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
                $grid[] = ['', round($lat, 6), round($lng, 6)];
            }
        }

        // Cache::put($district->id . '_nearby_progress', 0);

        $record = new Record();
        $record->resource = "從行政區域建立: {$district->name}";
        $record->process = 0;
        $record->total = count($grid);
        $record->save();

        CreateGridJob::dispatch($grid, $record)->onQueue('default');

        $district->processed = true;
        $district->save();

        return response()->json(["message" => "Success"]);
    }

    public function importCSV(Request $request)
    {
        $csvFile = $request->file('csv');

        if (!$csvFile || !$csvFile->isValid()) {
            return response()->json(['message' => 'Invalid CSV file'], 400);
        }

        $grid = [];

        $handle = fopen($csvFile->getRealPath(), 'r');
        if ($handle !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                // 假設 CSV 每行格式為: lat, lng
                if (count($data) >= 2) {
                    $name = $data[0] ?? '';
                    $lat = floatval($data[1]);
                    $lng = floatval($data[2]);
                    $grid[] = [$name, $lat, $lng];
                }
            }
            fclose($handle);
        }

        $record = new Record();
        $record->resource = "從CSV建立: {$csvFile->getClientOriginalName()}";
        $record->process = 0;
        $record->total = count($grid);
        $record->save();

        CreateGridJob::dispatch($grid, $record)->onQueue('default');

        return response()->json(["message" => "Success"]);
    }
}
