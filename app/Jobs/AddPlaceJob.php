<?php

namespace App\Jobs;

use App\Models\Grid;
use App\Models\Place;
use App\Models\Places;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddPlaceJob implements ShouldQueue
{
    use Queueable;
    public $grid;
    public $record;
    public $resource = '';

    /**
     * Create a new job instance.
     */
    public function __construct($grid, $record, $resource)
    {
        $this->grid = $grid;
        $this->record = $record;
        $this->resource = $resource;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $grid = $this->grid;
        $resource = $this->resource;

        // 更據他是何種 Resource 來決定如何處理
        if ($resource == 'google') {
            $this->handleGooglePlaces($grid);
        }
    }

    private function handleGooglePlaces(Grid $grid): void
    {
        $folder = "app/places";
        $filename = storage_path("{$folder}/{$grid->id}.json");
        if (file_exists($filename)) {
            $json = file_get_contents($filename);
            $data = json_decode($json, true);
            $places = $data['places'] ?? [];

            foreach ($places as $data) {

                $resource['resource'] = 'google';
                $resource['unique_id'] = $data['id'] ?? null;
                $resource['name'] = $data['displayName']['text'] ?? "未知";
                $resource['rating'] = $data['rating'] ?? null;
                $resource['user_rating_count'] = $data['userRatingCount'] ?? null;
                $resource['formatted_address'] = $data['formattedAddress'] ?? null;
                $resource['google_maps_uri'] = $data['googleMapsUri'] ?? null;
                $resource['lat'] = $data['location']['latitude'] ?? null;
                $resource['lng'] = $data['location']['longitude'] ?? null;
                $resource['types'] = isset($data['types']) ? implode(',', $data['types']) : null;
                $resource['grid_id'] = $grid->id;

                // Save the place to the database
                // if (!isset($data['formattedAddress']) || strpos($data['formattedAddress'], '高雄') === false) {
                //     continue; // Skip places not in Kaohsiung
                // }

                $place = Place::updateOrCreate(['unique_id' => $data['id'], 'resource' => 'google'], $resource);

                $this->record->places()->attach($place->id);

                foreach ($data['reviews'] ?? [] as $review) {
                    $place->reviews()->create([
                        'name' => $review["name"] ?? "",
                        'relativePublishTimeDescription' => $review["relativePublishTimeDescription"] ?? "",
                        'rating' => $review["rating"] ?? "",
                        'text' => $review["text"]["text"] ?? "",
                        'authorName' => $review["authorAttribution"]["displayName"] ?? ""
                    ]);
                }
            }
        }
    }
}
