<?php

use App\Http\Controllers\Frontend\EventMicrositeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public Flagship Event Microsite
Route::get('/', [EventMicrositeController::class, 'show'])->name('home');

// Specific Event by Slug
Route::get('/events/{slug}', [EventMicrositeController::class, 'show'])->name('events.show');

// All Pujas Directory
Route::get('/all-pujas', [EventMicrositeController::class, 'allPujas'])->name('all-pujas');
Route::get('/events/{slug}/all-pujas', [EventMicrositeController::class, 'allPujas'])->name('events.all-pujas');

// Dedicated Voting Flow (Accessible strictly via SEO slug)
Route::get('/vote/{participant}', [EventMicrositeController::class, 'vote'])->name('vote.participant');

// Vote Confirmed & Voucher Success Flow
Route::get('/vote-success/{participant}', [EventMicrositeController::class, 'voteSuccess'])->name('vote-success.participant');

// Admin Utility: Create storage symbolic link via authenticated session
Route::get('/admin-tools/storage-link', function () {
    Artisan::call('storage:link');

    return response('Storage link created successfully.', 200);
})->middleware('auth')->name('admin.tools.storage-link');
