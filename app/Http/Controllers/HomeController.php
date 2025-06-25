<?php

namespace App\Http\Controllers;

use App\Models\Grid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home()
    {
        return response()->json("Welcome to the Home Page!");
    }

    public function getGoogleGrid()
    {
        return view('google-grid');
    }

    public function getCSVGrid()
    {
        return view('csv-grid');
    }

    // public function postGoogleGrid(Request $request)
    // {
    //     $limit = config('services.google_places.max_requests', 10);
    //     $processed = Cache::get('today_request_count', 0);

    //     if ($processed >= $limit) {
    //         return response()->json([
    //             'error' => "Today has already processed {$processed} requests, which is the limit of {$limit}."
    //         ], 429);
    //     }

    //     $lng = $request->input('lng');
    //     $lat = $request->input('lat');
    //     $radius = $request->input('radius', 500); // 預設半徑為1000米

    //     if (!$lng || !$lat) {
    //         return response()->json(['error' => 'Longitude and Latitude are required'], 400);
    //     }

    //     // 正式的 Nearby Search
    //     $key = config('services.google_places.key');
    //     // 文件參考: https://developers.google.com/maps/documentation/places/web-service/nearby-search?hl=zh-tw
    //     $url = 'https://places.googleapis.com/v1/places:searchNearby';

    //     $fields = implode(",", [
    //         "places.id",
    //         "places.displayName",
    //         "places.formattedAddress",
    //         "places.types",
    //         "places.rating",
    //         "places.userRatingCount",
    //         "places.location",
    //         "places.googleMapsUri"
    //     ]);

    //     $headers = [
    //         "X-Goog-Api-Key" => $key,
    //         "X-Goog-FieldMask" => $fields,
    //         "Content-Type" => "application/json"
    //     ];

    //     $payload = [
    //         "locationRestriction" => [
    //             "circle" => [
    //                 "center" => ["latitude" => $lat, "longitude" => $lng],
    //                 "radius" => $radius
    //             ]
    //         ],
    //         "languageCode" => "zh-TW"
    //     ];

    //     $response = Http::withOptions([
    //         'verify' => false,
    //     ])->withHeaders($headers)->post($url, $payload);

    //     Cache::increment('today_request_count');

    //     if ($response->successful()) {
    //         $data = $response->json();
    //         return response()->json($data);
    //     } else {
    //         // 錯誤處理
    //         throw new \Exception("Error fetching data for {$lat}, {$lng}: " . $response->body());
    //     }
    // }

    public function getApiGrid($gridId)
    {
        return Grid::find($gridId)
            ? response()->json(Grid::find($gridId))
            : response()->json(['error' => 'Grid not found'], 404);
    }
}
