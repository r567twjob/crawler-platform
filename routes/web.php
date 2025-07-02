<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JSONController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProcessController;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/google-grid', [HomeController::class, 'getGoogleGrid'])->name('google-grid.get');
Route::get('/csv-grid', [HomeController::class, 'getCSVGrid'])->name('csv-grid.get');
Route::post('/google-grid', [HomeController::class, 'postGoogleGrid'])->name('google-grid.post');
Route::get('/test-grid', [TestController::class, 'process_grid']);

Route::post('/csv-import', [ProcessController::class, 'importCSV'])->name('csv.import');

Route::get('json', [JSONController::class, 'processJSON'])->name('processJSON');
