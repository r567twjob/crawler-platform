<?php

namespace App\Jobs;

use App\Models\Place;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class CreatePlaceFromJsonJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $file)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $content =  Storage::disk('local')->get($this->file);
        $places = json_decode($content, true);

        foreach ($places as $key => $data) {
            $resource['resource'] = 'google';
            $resource['unique_id'] = $data['place_id'] ?? null;
            $resource['name'] = $data['name'] ?? "未知";
            $resource['rating'] = intval($data['rating']) ?? null;
            $resource['user_rating_count'] = $data['reviews_count'] ?? null;
            $resource['formatted_address'] = $data['address'] ?? null;
            // $resource['google_maps_uri'] = $data['googleMapsUri'] ?? null;
            $resource['lat'] = $data['lat'] ?? null;
            $resource['lng'] = $data['lng'] ?? null;
            $resource['types'] = isset($data['types']) ? implode(',', $data['types']) : null;

            $place = Place::updateOrCreate(['unique_id' => $data['place_id'], 'resource' => 'google'], $resource);

            foreach ($data['reviews'] ?? [] as $review) {
                $place->reviews()->create([
                    'name' => $review["author"] ?? "",
                    'relativePublishTimeDescription' => "",
                    'rating' => $review["rating"] ?? "",
                    'text' => $review["text"]["text"] ?? "",
                    'authorName' => $review["author"] ?? ""
                ]);
            }
        }
    }
}
