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

    /**
     * Create a new job instance.
     */
    public function __construct($grid)
    {
        //
        $this->grid = $grid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $grid = $this->grid;

        $folder = "app/places/{$grid->district->id}";
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
                $resource['types'] = isset($data['types']) ? implode(',', $data['types']) : null;

                // Save the place to the database
                Place::updateOrCreate(
                    ['unique_id' => $data['id']],
                    $resource
                );
            }
        }
    }
}
