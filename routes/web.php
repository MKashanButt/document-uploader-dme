<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()
        ->route('dashboard.index');
});

Route::resource('dashboard', DocumentController::class)
    ->middleware(['auth', 'verified']);

Route::post('/dashboard/search', [DocumentController::class, 'search'])
    ->name('dashboard.search')
    ->middleware(['auth', 'verified']);

Route::delete('documents/{document}', [DocumentController::class, 'destroy'])
    ->name('documents.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
