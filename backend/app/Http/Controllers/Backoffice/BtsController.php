<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BtsStoreRequest;
use App\Models\Bts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BtsController extends Controller
{
    public function index(): Response
    {
        $items = Bts::query()->latest()->paginate(10)->withQueryString();
        return Inertia::render('Backoffice/Bts/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Backoffice/Bts/Create');
    }

    public function show(Bts $bts): Response
    {
        $bts->loadCount([
            'clients as clients_total',
            'clients as clients_actifs' => fn ($query) => $query->where('statut', 'actif'),
        ]);

        $clients = $bts->clients()
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($client) => [
                'id' => $client->id,
                'code' => $client->code,
                'nom' => $client->nom,
                'statut' => $client->statut,
                'telephone' => $client->telephone,
                'lat' => $client->lat,
                'lng' => $client->lng,
            ]);

        return Inertia::render('Backoffice/Bts/Show', [
            'bts' => [
                'id' => $bts->id,
                'code' => $bts->code,
                'ville' => $bts->ville,
                'lat' => $bts->lat,
                'lng' => $bts->lng,
                'composants' => $bts->composants,
                'clients_total' => $bts->clients_total ?? 0,
                'clients_actifs' => $bts->clients_actifs ?? 0,
            ],
            'clients' => $clients,
        ]);
    }

    public function store(BtsStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['composants'] = $data['composants'] ?? [];
        // Génération code unique simple
        $prefix = 'BTS-' . Str::upper(Str::slug(Str::substr($data['ville'], 0, 6), ''));
        do {
            $code = $prefix . '-' . random_int(1000, 9999);
        } while (Bts::where('code', $code)->exists());
        $data['code'] = $code;

        Bts::create($data);
        return redirect()->route('backoffice.bts.index')->with('success', 'BTS créée');
    }
}
