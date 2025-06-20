<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/google-map-crawler', [\App\Http\Controllers\ProcessController::class, 'startNearbySearch'])->name('google_map_crawler');
// Route::get('/grid/{gridId}', [\App\Http\Controllers\HomeController::class, 'getApiGrid'])->name('grid.show');
