<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;

class NearbySearchJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3; // 限制最多重試 3 次

    protected $lat, $lng, $grid;

    public function __construct($lat, $lng, $grid)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->grid = $grid;
    }

    public function handle()
    {
        // test
        Cache::increment($this->grid->district->id . '_nearby_progress');
        return;
        //
        $key = config('services.google_places.key');
        $maxRequests = intval(config('services.google_places.max_requests', 10));
        $requestCount = Cache::get('today_request_count', 0);

        if ($requestCount >= $maxRequests) return;

        $url = 'https://places.googleapis.com/v1/places:searchNearby';

        $fields = implode(",", [
            "places.id",
            // "places.name",
            "places.displayName",
            "places.formattedAddress",
            "places.types",
            "places.rating",
            "places.userRatingCount",
            "places.location",
            "places.googleMapsUri",
            // "places.photos",
            // "places.reviews"

        ]);

        $headers = [
            "X-Goog-Api-Key" => $key,
            "X-Goog-FieldMask" => $fields,
            "Content-Type" => "application/json"
        ];

        $payload = [
            "locationRestriction" => [
                "circle" => [
                    "center" => ["latitude" => $this->lat, "longitude" => $this->lng],
                    "radius" => 500
                ]
            ],
            "languageCode" => "zh-TW"
        ];

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            Cache::increment($this->grid->district->id . '_nearby_progress');
            // 儲存資料
            $folder = "app/places/{$this->grid->district->id}";
            $directory = storage_path($folder);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $filename = storage_path("{$folder}/{$this->grid->id}.json");
            file_put_contents($filename, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // 丟到 Queue 裡面新增 Place 資料
            AddPlaceJob::dispatch($this->grid)->onQueue('default');
        } else {
            // 錯誤處理
            throw new \Exception("Error fetching data for {$this->lat}, {$this->lng}: " . $response->body());
        }
    }
}
