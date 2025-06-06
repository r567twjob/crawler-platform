<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/google-grid', [HomeController::class, 'getGoogleGrid'])->name('google-grid.get');
Route::post('/google-grid', [HomeController::class, 'postGoogleGrid'])->name('google-grid.post');
