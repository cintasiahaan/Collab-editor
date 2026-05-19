<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Document;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    // Jika sudah login -> langsung dashboard
    if (Auth::check()) {

        return redirect()->route('dashboard');
    }

    // Jika belum login -> welcome page
    return Inertia::render('Welcome', [

        'canLogin' => Route::has('login'),

        'canRegister' => Route::has('register'),

        'laravelVersion' => Application::VERSION,

        'phpVersion' => PHP_VERSION,

        'totalDocuments' => Document::count(),
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTH REQUIRED
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DocumentController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */

    // Create document
    Route::post('/documents', [DocumentController::class, 'store'])
        ->name('documents.store');

    // Open editor
    Route::get('/documents/{document:slug}', [DocumentController::class, 'show'])
        ->name('documents.show');

    // Update autosave/title
    Route::put('/documents/{document:slug}', [DocumentController::class, 'update'])
        ->name('documents.update');

    // Delete document
    Route::delete('/documents/{document:slug}', [DocumentController::class, 'destroy'])
        ->name('documents.destroy');

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */

    // Restore version
    Route::post(
        '/documents/{document:slug}/restore/{revision}',
        [DocumentController::class, 'restore']
    )->name('documents.restore');

    // Delete all history
    Route::delete(
        '/documents/{document:slug}/history',
        [DocumentController::class, 'deleteAllHistory']
    )->name('documents.history.destroy');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
