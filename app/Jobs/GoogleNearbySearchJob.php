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
            "excludedPrimaryTypes" => [
                "car_dealer",
                "car_repair",
                "car_rental", //這裡確認一下
                "car_wash",
                "gas_station", //這裡確認一下
                "electric_vehicle_charging_station", //這裡確認一下
                "parking", //這裡確認一下
                "rest_stop", //這裡確認一下
                "corporate_office",
                // 學校(教育)類型
                "library",
                "preschool",
                "primary_school",
                "secondary_school",
                "university",
                "school",
                // 設施類型
                "public_bath",
                "public_bathroom",
                "stable", //這裡確認一下
                // 財經類型
                "accounting",
                "atm",
                "bank",
                // 地理區域
                "administrative_area_level_1",
                "administrative_area_level_2",
                "country",
                "locality",
                "postal_code",
                "school_district",
                // 政府機關
                "city_hall",
                "courthouse",
                "embassy",
                "fire_station",
                "government_office",
                "local_government_office",
                "police",
                "post_office",
                // 健康與保健
                "chiropractor",
                "dental_clinic",
                "dentist",
                "doctor",
                "drugstore",
                "hospital",
                "medical_lab",
                "pharmacy",
                "physiotherapist",
                // 住宅
                "apartment_building",
                "apartment_complex",
                "condominium_complex",
                "housing_complex"
            ],
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
            // Cache::increment($this->grid->district->id . '_nearby_progress');

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
