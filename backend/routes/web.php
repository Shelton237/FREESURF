<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $stats = [
        'clients' => \App\Models\Client::count(),
        'bts' => \App\Models\Bts::count(),
        'demandes' => \App\Models\Demande::count(),
    ];
    // Séries simples sur 14 jours pour la vitrine
    $from = now()->subDays(13)->startOfDay();
    $clientsSeries = \App\Models\Client::where('created_at', '>=', $from)
        ->selectRaw('date(created_at) as d, count(*) as c')
        ->groupBy('d')->orderBy('d')->pluck('c', 'd');
    $demandesSeries = \App\Models\Demande::where('created_at', '>=', $from)
        ->selectRaw('date(created_at) as d, count(*) as c')
        ->groupBy('d')->orderBy('d')->pluck('c', 'd');
    $days = collect(range(0, 13))->map(fn($i) => $from->copy()->addDays($i)->toDateString());
    $series = [
        'clients' => $days->map(fn($d) => (int) ($clientsSeries[$d] ?? 0))->values(),
        'demandes' => $days->map(fn($d) => (int) ($demandesSeries[$d] ?? 0))->values(),
        'labels' => $days,
    ];
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'stats' => $stats,
        'appName' => config('app.name', 'FREESURF'),
        'series' => $series,
        // Slides / témoignages / actus de démonstration — à brancher sur DB plus tard
        'slides' => [
            ['image' => '/logo.png', 'title' => 'Portail client', 'text' => 'Demande d\'abonnement en quelques clics.'],
            ['image' => '/logo_black.png', 'title' => 'Backoffice', 'text' => 'Gérez BTS, clients, factures et paiements.'],
            ['image' => '/logo.png', 'title' => 'PWA mobile', 'text' => 'Installable, offline pour le terrain.'],
        ],
        'testimonials' => [
            ['name' => 'A. Kamdem', 'role' => 'Technicien', 'text' => 'Les visites d\'éligibilité sont plus rapides avec le portail.'],
            ['name' => 'M. Tchoumi', 'role' => 'Partenaire', 'text' => 'En quelques minutes, je soumets une demande client.'],
            ['name' => 'S. Nguem', 'role' => 'Comptable', 'text' => 'La facturation mensuelle automatisée nous fait gagner du temps.'],
        ],
        'news' => [
            ['title' => 'Lancement CuWiP', 'date' => now()->toDateString(), 'text' => 'Première version disponible pour pilote.'],
            ['title' => 'Module facturation', 'date' => now()->addDays(7)->toDateString(), 'text' => 'Arrive bientôt avec PDF et relances.'],
        ],
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
Route::prefix('backoffice')->middleware(['auth', 'verified','role:backoffice'])->group(function () {
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

    Route::get('/work-orders/create', [\App\Http\Controllers\Backoffice\WorkOrderController::class, 'create'])->name('backoffice.work-orders.create');
    Route::post('/work-orders', [\App\Http\Controllers\Backoffice\WorkOrderController::class, 'store'])->name('backoffice.work-orders.store');

    Route::get('/admin/users', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'index'])->name('backoffice.admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'create'])->name('backoffice.admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'store'])->name('backoffice.admin.users.store');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'edit'])->name('backoffice.admin.users.edit');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'update'])->name('backoffice.admin.users.update');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\Backoffice\AdminUserController::class, 'destroy'])->name('backoffice.admin.users.destroy');
});

// Espace technicien
Route::prefix('tech')->middleware(['auth','verified','role:technicien'])->group(function(){
    Route::get('/', [\App\Http\Controllers\Tech\WorkOrderController::class, 'index'])->name('tech.dashboard');
    Route::get('/work-orders/{workOrder}', [\App\Http\Controllers\Tech\WorkOrderController::class, 'show'])->name('tech.work-orders.show');
    Route::post('/work-orders/{workOrder}/start', [\App\Http\Controllers\Tech\WorkOrderController::class, 'start'])->name('tech.work-orders.start');
    Route::post('/work-orders/{workOrder}/complete', [\App\Http\Controllers\Tech\WorkOrderController::class, 'complete'])->name('tech.work-orders.complete');
    Route::post('/work-orders/{workOrder}/attachments', [\App\Http\Controllers\Tech\WorkOrderController::class, 'uploadAttachment'])->name('tech.work-orders.attachments.upload');
});
