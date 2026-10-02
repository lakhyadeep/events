<?php

use App\Http\Controllers\Frontend\EventMicrositeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public Flagship Event Microsite
Route::get('/', [EventMicrositeController::class, 'show'])->name('home');

// Specific Event by Slug
Route::get('/events/{slug}', [EventMicrositeController::class, 'show'])->name('events.show');

// Admin Utility: Create storage symbolic link via authenticated session
Route::get('/admin-tools/storage-link', function () {
    Artisan::call('storage:link');

    return response('Storage link created successfully.', 200);
})->middleware('auth')->name('admin.tools.storage-link');
