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

        return Inertia::render('Backoffice/Dashboard', [
            'stats' => $stats,
            'clientsByBts' => $clientsByBts,
            'series' => $series,
        ]);
    }
}
