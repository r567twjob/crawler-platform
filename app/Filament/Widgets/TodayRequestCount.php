<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class TodayRequestCount extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("今日已請求數", Cache::get('today_request_count', 0)),
            Stat::make("目前請求限制", config('services.google_places.max_requests', 10))
        ];
    }
}
