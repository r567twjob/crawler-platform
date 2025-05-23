<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/google-map-crawler', [\App\Http\Controllers\ProcessController::class, 'startNearbySearch'])->name('google_map_crawler');
