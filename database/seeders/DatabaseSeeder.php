<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\PlaceType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'jimmy',
            'email' => 'r567twjob@gmail.com',
            'password' => bcrypt('qwert231')
        ]);


        District::factory()->create([
            "name" => "永康區",
            "lat_min" => 23,
            "lat_max" => 23.07,
            "lng_min" => 120.21,
            "lng_max" => 120.3
        ]);

        District::factory()->create([
            "name" => "中西區",
            "lat_min" => 22.984,
            "lat_max" => 23.0085,
            "lng_min" => 120.185,
            "lng_max" => 120.225
        ]);

        District::factory()->create([
            "name" => "東區",
            "lat_min" => 22.971,
            "lat_max" => 23.0005,
            "lng_min" => 120.228,
            "lng_max" => 120.2655
        ]);

        District::factory()->create([
            "name" => "北區",
            "lat_min" => 23,
            "lat_max" => 23.05,
            "lng_min" => 120.18,
            "lng_max" => 120.23
        ]);


        District::factory()->create([
            "name" => "南區",
            "lat_min" => 22.94,
            "lat_max" => 23,
            "lng_min" => 120.18,
            "lng_max" => 120.23
        ]);

        District::factory()->create([
            "name" => "安平區",
            "lat_min" => 22.983,
            "lat_max" => 23.0125,
            "lng_min" => 120.153,
            "lng_max" => 120.1835
        ]);

        // 讀取 Storage app 中的 google_map_types.csv 檔案
        $filePath = storage_path('app/google_map_types.csv');
        $types = array_map('str_getcsv', file($filePath));
        foreach ($types as $type) {
            PlaceType::factory()->create([
                'resource' => 'google',
                'label' => $type[1],
                'key'  => $type[0]
            ]);
        }
    }
}
