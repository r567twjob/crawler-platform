<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;

class GoogleNearbySearchJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3; // 限制最多重試 3 次

    protected $lat, $lng, $grid, $record, $key;

    public function __construct($lat, $lng, $grid, $record, $key)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->grid = $grid;
        $this->record = $record;
        $this->key = $key;
    }

    public function handle()
    {

        // 正式的 Nearby Search
        $keys = config('services.google_places.keys');
        $key = $keys[$this->key];

        // 文件參考: https://developers.google.com/maps/documentation/places/web-service/nearby-search?hl=zh-tw
        $url = 'https://places.googleapis.com/v1/places:searchNearby';

        $fields = implode(",", [
            "places.id",
            "places.displayName",
            "places.formattedAddress",
            "places.types",
            "places.rating",
            "places.userRatingCount",
            "places.location",
            "places.googleMapsUri",
            "places.photos",
            "places.reviews",
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

            // 儲存資料(JSON)
            $folder = "app/places";
            $directory = storage_path($folder);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $filename = storage_path("{$folder}/{$this->grid->id}.json");
            file_put_contents($filename, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // 丟到 Queue 裡面新增 Place 資料
            AddPlaceJob::dispatch($this->grid, $this->record, 'google')->onQueue('default');

            // 更新 grid 的 places_count
            $this->grid->place_count = count($data['places'] ?? []);
            $this->grid->save();
        } else {
            // 錯誤處理
            throw new \Exception("Error fetching data for {$this->lat}, {$this->lng}: " . $response->body());
        }
    }
}
