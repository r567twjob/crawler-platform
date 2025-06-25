<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DailyClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-clear-cache';

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
        // 清除昨天的快取
        $yesterday = now()->subDay()->toDateString();
        // 假設你有多個 key，可以用 Cache::get/put 追蹤所有 key，這裡假設只有一個 key
        // 如果有多個 key，請自行維護一個 key 列表
        $providers = config('services.google_places.keys');

        foreach ($providers as $key => $info) {
            Cache::forget("api_limit_{$key}_" . $yesterday);
        }

        $jobs = DB::table('jobs')->where('queue', 'pending');

        foreach ($jobs->get() as $job) {
            DB::table('jobs')->where('id', $job->id)->update(['queue' => $this->selectAvailableApiKey()]);
        }
    }

    private function selectAvailableApiKey(): string
    {
        $providers = config('services.google_places.keys');
        $limit = config('services.google_places.max_requests', 10);

        foreach ($providers as $key => $info) {
            $usage = Cache::get("api_limit_{$key}_" . now()->toDateString());

            // 如果當天沒有使用記錄，則初始化為0
            if ($usage === null) {
                Cache::put("api_limit_{$key}_" . now()->toDateString(), 0, 86400); // 24 hours
                $usage = 0;
            }

            if ($usage < $limit) {
                Cache::increment("api_limit_{$key}_" . now()->toDateString());
                return $key;
            }
        }

        return 'pending';
    }
}
