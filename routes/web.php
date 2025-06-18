<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/google-grid', [HomeController::class, 'getGoogleGrid'])->name('google-grid.get');
Route::post('/google-grid', [HomeController::class, 'postGoogleGrid'])->name('google-grid.post');

Route::get('/test-grid', [TestController::class, 'process_grid']);
