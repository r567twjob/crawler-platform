<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class ProcessJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = config('services.google_places.max_requests', 10);
        $processed = Cache::get('today_request_count', 0);

        if ($processed >= $limit) {
            $this->info("Today has already processed {$processed} requests, which is the limit of {$limit}.");
            return;
        } else {
            // 如果這裡可以判斷 queue 是否有任務. 或許會更妥當?
            $jobCount = Queue::size('nearby_search');

            if ($jobCount === 0) {
                $this->info("No jobs in the 'nearby_search' queue. Exiting.");
                return;
            } else {
                Artisan::call('queue:work', [
                    '--once' => true,
                    '--queue' => 'nearby_search',
                    '--timeout' => 60,
                ]);
                // Cache::increment('today_request_count');
            }
        }
    }
}
