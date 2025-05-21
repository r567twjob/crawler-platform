<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/google', [\App\Http\Controllers\HomeController::class, 'getGooglePOIs'])->name('getGooglePOIs');
// Route::get('/osm', [\App\Http\Controllers\HomeController::class, 'fetchOsmPOIs']);

Route::post('/start-nearby-search', [\App\Http\Controllers\ProcessController::class, 'startNearbySearch'])->name('startNearbySearch');
Route::get('/progress', [\App\Http\Controllers\ProcessController::class, 'getProgress'])->name('getProgress');
Route::get('/list', [\App\Http\Controllers\HomeController::class, 'getList'])->name('list');
Route::get('/test/mail', [\App\Http\Controllers\TestController::class, 'testMail'])->name('testMail');
