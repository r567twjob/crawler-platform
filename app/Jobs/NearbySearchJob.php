<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;

class NearbySearchJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3; // 限制最多重試 3 次

    protected $lat, $lng, $district;

    public function __construct($lat, $lng, $district)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->district = $district;
    }

    public function handle()
    {
        $key = config('services.google_places.key');
        $maxRequests = intval(config('services.google_places.max_requests', 10));
        $requestCount = Cache::get($this->district . '_nearby_request_count', 0);

        if ($requestCount >= $maxRequests) return;

        $url = 'https://places.googleapis.com/v1/places:searchNearby';

        $fields = implode(",", [
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
            Cache::increment($this->district . '_nearby_request_count');
            Cache::increment($this->district . '_nearby_progress');

            // 儲存資料
            $directory = storage_path("app/{$this->district}");
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $filename = storage_path("app/{$this->district}/{$this->district}_{$this->lat}_{$this->lng}.json");
            file_put_contents($filename, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            // 錯誤處理
            throw new \Exception("Error fetching data for {$this->lat}, {$this->lng}: " . $response->body());
        }
    }
}
