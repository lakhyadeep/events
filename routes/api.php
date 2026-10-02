<?php

use App\Http\Controllers\Api\EventApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Version 1 Headless Event Microsite API with Rate Limiting
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/events/{slug}', [EventApiController::class, 'show']);
    Route::get('/events/{slug}/participants', [EventApiController::class, 'participants']);
    Route::get('/events/{slug}/shorts', [EventApiController::class, 'shorts']);
    Route::get('/events/{slug}/winners', [EventApiController::class, 'winners']);
});
