<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Models\Bts;
use App\Models\Client;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        $items = Client::with(['bts', 'partner'])->latest()->paginate(10)->withQueryString();
        return Inertia::render('Backoffice/Clients/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Backoffice/Clients/Create', [
            'bts' => Bts::orderBy('ville')->get(['id', 'code', 'ville']),
            'partners' => Partner::orderBy('nom')->get(['id', 'nom']),
        ]);
    }

    public function store(ClientStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['photos'] = $data['photos'] ?? [];
        $prefix = 'CLI-' . Str::upper(Str::slug(Str::substr($data['nom'], 0, 6), ''));
        do {
            $code = $prefix . '-' . random_int(10000, 99999);
        } while (Client::where('code', $code)->exists());
        $data['code'] = $code;
        $data['statut'] = 'prospect';

        Client::create($data);
        return redirect()->route('backoffice.clients.index')->with('success', 'Client créé');
    }
}

