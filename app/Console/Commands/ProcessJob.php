<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

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
        $limit = env('NEARBYSEARCH_MAX_REQUESTS', 100);
        $processed = Cache::get('today_request_count', 0);

        if ($processed > $limit) {
            $this->info("Today has already processed {$processed} requests, which is the limit of {$limit}.");
            return;
        } else {
            Artisan::call('queue:work', [
                '--once' => true,
                '--queue' => 'default',
                '--timeout' => 60,
            ]);
            Cache::increment('today_request_count');
        }
    }
}
