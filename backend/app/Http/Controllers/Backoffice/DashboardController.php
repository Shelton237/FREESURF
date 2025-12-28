<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Bts;
use App\Models\Client;
use App\Models\Demande;
use App\Models\WorkOrder;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $stats = [
            'clients_total' => Client::count(),
            'clients_actifs' => Client::where('statut', 'actif')->count(),
            'bts' => Bts::count(),
            'demandes_en_attente' => Demande::whereNull('client_id')->count(),
            'interventions_en_cours' => WorkOrder::whereIn('status', ['assigned','accepted','on_site'])->count(),
        ];

        $clientsByBts = Client::selectRaw('bts_id, count(*) as total')
            ->whereNotNull('bts_id')
            ->groupBy('bts_id')
            ->with('bts:id,code,ville')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'bts' => $row->bts?->code ?? 'N/A',
                'ville' => $row->bts?->ville ?? '-',
                'total' => $row->total,
            ]);

        $from = Carbon::now()->subMonths(5)->startOfMonth();
        $clientsMonthly = Client::where('created_at', '>=', $from)
            ->get()
            ->groupBy(fn ($client) => $client->created_at?->format('Y-m'))
            ->map->count();

        $months = collect(range(0,5))->map(fn ($i) => $from->copy()->addMonths($i)->format('Y-m'));
        $series = [
            'labels' => $months,
            'clients' => $months->map(fn ($m) => (int) ($clientsMonthly[$m] ?? 0)),
        ];

        $btsWithCoords = Bts::select('id', 'code', 'ville', 'lat', 'lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->withCount([
                'clients as clients_actifs' => function ($query) {
                    $query->where('statut', 'actif');
                },
            ])
            ->get();

        $mapBts = $btsWithCoords->map(fn ($bts) => [
            'id' => $bts->id,
            'code' => $bts->code,
            'ville' => $bts->ville,
            'lat' => (float) $bts->lat,
            'lng' => (float) $bts->lng,
            'clients' => (int) ($bts->clients_actifs ?? 0),
            'url' => route('backoffice.bts.show', $bts),
        ]);

        $clientsWithCoords = Client::select('id', 'code', 'nom', 'lat', 'lng', 'statut', 'bts_id', 'updated_at')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->orderByDesc('updated_at')
            ->limit(100)
            ->with('bts:id,code')
            ->get();

        $mapClients = $clientsWithCoords->map(fn ($client) => [
            'id' => $client->id,
            'code' => $client->code,
            'nom' => $client->nom,
            'statut' => $client->statut,
            'lat' => (float) $client->lat,
            'lng' => (float) $client->lng,
            'bts' => $client->bts?->code,
            'url' => route('backoffice.clients.show', $client),
        ]);

        $defaultBounds = [
            'southWest' => ['lat' => 3.4, 'lng' => 9.3],
            'northEast' => ['lat' => 4.6, 'lng' => 11.9],
        ];

        $map = [
            'bts' => $mapBts,
            'clients' => $mapClients,
            'center' => [
                'lat' => $btsWithCoords->avg('lat') ?? $clientsWithCoords->avg('lat') ?? 3.95,
                'lng' => $btsWithCoords->avg('lng') ?? $clientsWithCoords->avg('lng') ?? 10.6,
            ],
            'bounds' => $defaultBounds,
        ];

        $filters = [
            'villes' => Bts::select('ville')->whereNotNull('ville')->distinct()->orderBy('ville')->pluck('ville'),
            'statuts' => Client::select('statut')->whereNotNull('statut')->distinct()->orderBy('statut')->pluck('statut'),
        ];

        return Inertia::render('Backoffice/Dashboard', [
            'stats' => $stats,
            'clientsByBts' => $clientsByBts,
            'series' => $series,
            'map' => $map,
            'filters' => $filters,
        ]);
    }
}
