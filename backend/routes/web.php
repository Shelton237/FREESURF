<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Portail (Espace Client)
Route::prefix('portal')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Portal/Home');
    })->name('portal.home');
    Route::get('/demandes/nouvelle', [\App\Http\Controllers\Portal\DemandeController::class, 'create'])->name('portal.demandes.create');
    Route::post('/demandes', [\App\Http\Controllers\Portal\DemandeController::class, 'store'])->name('portal.demandes.store');
});

// Backoffice (Administration)
Route::prefix('backoffice')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Backoffice/Dashboard');
    })->name('backoffice.dashboard');
    Route::get('/bts', [\App\Http\Controllers\Backoffice\BtsController::class, 'index'])->name('backoffice.bts.index');
    Route::get('/bts/create', [\App\Http\Controllers\Backoffice\BtsController::class, 'create'])->name('backoffice.bts.create');
    Route::post('/bts', [\App\Http\Controllers\Backoffice\BtsController::class, 'store'])->name('backoffice.bts.store');

    Route::get('/clients', [\App\Http\Controllers\Backoffice\ClientController::class, 'index'])->name('backoffice.clients.index');
    Route::get('/clients/create', [\App\Http\Controllers\Backoffice\ClientController::class, 'create'])->name('backoffice.clients.create');
    Route::post('/clients', [\App\Http\Controllers\Backoffice\ClientController::class, 'store'])->name('backoffice.clients.store');
    Route::get('/clients/{client}', [\App\Http\Controllers\Backoffice\ClientController::class, 'show'])->name('backoffice.clients.show');
    Route::post('/clients/{client}/eligibilites', [\App\Http\Controllers\Backoffice\ClientController::class, 'storeEligibilite'])->name('backoffice.clients.eligibilites.store');
    Route::post('/clients/{client}/installation/complete', [\App\Http\Controllers\Backoffice\ClientController::class, 'completeInstallation'])->name('backoffice.clients.installation.complete');
});
