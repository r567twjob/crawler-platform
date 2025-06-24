<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\GoogleNearbySearchJob;
use App\Models\District;
use App\Models\Grid;
use App\Models\Record;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class CreateGridJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3;
    public $timeout = 120;
    public $grids;
    public $record;

    /**
     * Create a new job instance.
     */
    public function __construct($grids, $record)
    {
        $this->grids = $grids;
        $this->record = $record;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $record = $this->record;
        foreach ($this->grids as [$name, $lat, $lng]) {
            $grid = Grid::create([
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng
            ]);
            $queue_name = $this->selectAvailableApiKey(); // 可以用輪詢或權重機制
            GoogleNearbySearchJob::dispatch($lat, $lng, $grid, $record, $queue_name)->onQueue($queue_name);
        }
    }

    private function selectAvailableApiKey(): string
    {
        $providers = config('services.google_places.keys');
        $limit = config('services.google_places.max_requests', 10);

        foreach ($providers as $key => $info) {
            $usage = Cache::get("api_limit_{$key}_" . now()->toDateString(), 0);
            if ($usage < $limit) {
                return $key;
            }
        }

        return 'pending';
    }
}
