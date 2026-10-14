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

// Dedicated Voting Flow (v2)
Route::get('/vote', [EventMicrositeController::class, 'vote'])->name('vote');
Route::get('/vote/{participant}', [EventMicrositeController::class, 'vote'])->name('vote.participant');
Route::get('/events/{slug}/vote', [EventMicrositeController::class, 'vote'])->name('events.vote');
Route::get('/events/{slug}/vote/{participant}', [EventMicrositeController::class, 'vote'])->name('events.vote.participant');

// Vote Confirmed & Voucher Success Flow (v2)
Route::get('/vote-success', [EventMicrositeController::class, 'voteSuccess'])->name('vote-success');
Route::get('/vote-success/{participant}', [EventMicrositeController::class, 'voteSuccess'])->name('vote-success.participant');
Route::get('/events/{slug}/vote-success', [EventMicrositeController::class, 'voteSuccess'])->name('events.vote-success');
Route::get('/events/{slug}/vote-success/{participant}', [EventMicrositeController::class, 'voteSuccess'])->name('events.vote-success.participant');

// Fallback GET handler for Boost browser logs endpoint
Route::get('/_boost/browser-logs', fn () => redirect()->route('home'));

// Admin Utility: Create storage symbolic link via authenticated session
Route::get('/admin-tools/storage-link', function () {
    Artisan::call('storage:link');

    return response('Storage link created successfully.', 200);
})->middleware('auth')->name('admin.tools.storage-link');
