<?php

use App\Http\Controllers\Frontend\EventMicrositeController;
use App\Models\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Root Domain: Redirect to current active event slug
Route::get('/', function () {
    $event = Event::where('status', 'active')->latest('year')->first()
        ?? Event::latest('year')->latest('id')->first();

    if (! $event) {
        return view('frontend.no-events');
    }

    return redirect()->route('events.show', ['event' => $event->slug]);
})->name('home');

// Direct All-Pujas URL: Redirect to current active event's all-pujas directory
Route::get('/all-pujas', function () {
    $event = Event::where('status', 'active')->latest('year')->first()
        ?? Event::latest('year')->latest('id')->first();

    if (! $event) {
        return view('frontend.no-events');
    }

    return redirect()->route('events.all-pujas', array_merge(['event' => $event->slug], request()->query()));
})->name('all-pujas.redirect');

// Direct Preview Awards URL: Redirect to current active event's preview-awards
Route::get('/preview-awards', function () {
    $event = Event::where('status', 'active')->latest('year')->first()
        ?? Event::latest('year')->latest('id')->first();

    if (! $event) {
        return view('frontend.no-events');
    }

    return redirect()->route('events.preview-awards', array_merge(['event' => $event->slug], request()->query()));
})->name('preview-awards.redirect');

// Admin Utility: Create storage symbolic link via authenticated session
Route::get('/admin-tools/storage-link', function () {
    Artisan::call('storage:link');

    return response('Storage link created successfully.', 200);
})->middleware('auth')->name('admin.tools.storage-link');

// All Event Routes prefixed with Event Slug: /{event}
Route::prefix('{event}')
    ->where(['event' => '^(?!admin|admin-tools|all-pujas|preview-awards|livewire|api|event-assets|storage|up)[a-zA-Z0-9_-]+$'])
    ->group(function () {
        Route::get('/', [EventMicrositeController::class, 'show'])->name('events.show');
        Route::get('/preview-awards', [EventMicrositeController::class, 'previewAwards'])->name('events.preview-awards');
        Route::get('/all-pujas', [EventMicrositeController::class, 'allPujas'])->name('events.all-pujas');
        Route::get('/vote/{participant}', [EventMicrositeController::class, 'vote'])->name('events.vote');
        Route::get('/vote-success/{participant}', [EventMicrositeController::class, 'voteSuccess'])->name('events.vote-success');
    });
