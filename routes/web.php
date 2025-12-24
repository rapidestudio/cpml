<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;



use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\Admin\ApplicantController as AdminApplicantController;

Route::get('/', function () {
    return redirect()->route('applicants.create');
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public Applicant Routes
Route::get('/apply', [ApplicantController::class, 'create'])->name('applicants.create');
Route::post('/apply', [ApplicantController::class, 'store'])->middleware('throttle:submission')->name('applicants.store');

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('applicants/export', [AdminApplicantController::class, 'export'])->name('applicants.export');
    Route::patch('applicants/bulk-status', [AdminApplicantController::class, 'bulkUpdateStatus'])->name('applicants.bulk-status');
    Route::delete('applicants/bulk-destroy', [AdminApplicantController::class, 'bulkDestroy'])->name('applicants.bulk-destroy');
    Route::delete('applicants/{applicant}', [AdminApplicantController::class, 'destroy'])->name('applicants.destroy');
    Route::resource('applicants', AdminApplicantController::class)->only(['index', 'show', 'update']);
    Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class);
});

require __DIR__.'/settings.php';
