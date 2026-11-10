<?php

use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/modules', [MarketingController::class, 'modules'])->name('modules');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('how-it-works');
Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');
