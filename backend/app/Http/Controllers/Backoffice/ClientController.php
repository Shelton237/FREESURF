<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Models\Bts;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Eligibilite;
use App\Models\Installation;
use App\Models\User;
use App\Http\Requests\EligibiliteStoreRequest;
use App\Http\Requests\InstallationCompleteRequest;
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

    public function show(Client $client): \Inertia\Response
    {
        $client->load(['bts', 'partner', 'eligibilites' => function($q){ $q->latest(); }, 'installation']);

        $savTickets = $client->savTickets()
            ->with('assignee:id,name')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($ticket) => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'assigned_to' => $ticket->assignee?->name,
                'channel' => $ticket->channel,
                'type' => $ticket->type,
                'created_at' => optional($ticket->created_at)->toDateTimeString(),
                'resolved_at' => optional($ticket->resolved_at)->toDateTimeString(),
            ]);

        $savOptions = [
            'types' => ['incident','assistance','reclamation'],
            'channels' => ['phone','whatsapp','portal','email'],
            'priorities' => ['low','normal','high'],
            'assignees' => User::whereIn('role', ['backoffice','technicien'])->orderBy('name')->get(['id','name']),
        ];

        return Inertia::render('Backoffice/Clients/Show', [
            'client' => $client,
            'savTickets' => $savTickets,
            'savOptions' => $savOptions,
        ]);
    }

    public function storeEligibilite(EligibiliteStoreRequest $request, Client $client): RedirectResponse
    {
        $data = $request->validated();
        $elig = new Eligibilite($data);
        $elig->user_id = auth()->id();
        $client->eligibilites()->save($elig);

        $client->statut = $data['resultat'] === 'eligible' ? 'eligible' : 'non_eligible';
        $client->save();

        return back()->with('success', 'Éligibilité enregistrée');
    }

    public function completeInstallation(InstallationCompleteRequest $request, Client $client): RedirectResponse
    {
        $data = $request->validated();
        $inst = $client->installation ?: new Installation(['client_id' => $client->id]);
        $inst->fill($data);
        $inst->terminee = true;
        $inst->user_id = auth()->id();
        $inst->save();

        $client->statut = 'actif';
        $client->save();

        return back()->with('success', "Installation marquée terminée");
    }
}
