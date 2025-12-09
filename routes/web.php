<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;


use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\Admin\ApplicantController as AdminApplicantController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public Applicant Routes
Route::get('/apply', [ApplicantController::class, 'create'])->name('applicants.create');
Route::post('/apply', [ApplicantController::class, 'store'])->name('applicants.store');

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('applicants', AdminApplicantController::class)->only(['index', 'show']);
});

require __DIR__.'/settings.php';
