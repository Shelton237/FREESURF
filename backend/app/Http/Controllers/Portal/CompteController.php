<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CompteClient;
use App\Models\Demande;
use App\Models\Facture;
use App\Models\LienCompteClient;
use App\Models\SavTicket;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompteController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Portal/Compte/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telephone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $compte = CompteClient::where('telephone', $validated['telephone'])->first();

        if (! $compte || ! $compte->password || ! Hash::check($validated['password'], $compte->password)) {
            return back()->with('error', 'Identifiants incorrects.');
        }

        $request->session()->put('portal_compte_id', $compte->id);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Connexion rǸussie.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('portal_compte_id');

        return redirect()->route('portal.home')->with('success', 'DǸconnectǸ.');
    }

    public function dashboard(Request $request): Response
    {
        $compte = $this->portalCompte($request, ['liens.client']);
        $linkedClientIds = $this->linkedClientIds($compte);

        $demandes = Demande::where('compte_client_id', $compte->id)
            ->latest()
            ->get(['id', 'type', 'statut', 'created_at', 'adresse'])
            ->map(fn (Demande $demande) => [
                'id' => $demande->id,
                'type' => $demande->type,
                'statut' => $demande->statut,
                'adresse' => $demande->adresse,
                'created_at' => $demande->created_at?->toDateTimeString(),
            ]);

        $facturesRecentes = $linkedClientIds->isEmpty()
            ? collect()
            : Facture::with('client')
                ->whereIn('client_id', $linkedClientIds)
                ->latest('due_date')
                ->limit(5)
                ->get();

        $tickets = $linkedClientIds->isEmpty()
            ? collect()
            : SavTicket::whereIn('client_id', $linkedClientIds)
                ->latest()
                ->limit(3)
                ->get(['id', 'client_id', 'subject', 'status', 'priority', 'created_at']);

        $impayees = $linkedClientIds->isEmpty()
            ? collect()
            : Facture::selectRaw('client_id, count(*) as total')
                ->whereIn('client_id', $linkedClientIds)
                ->whereNotIn('statut', ['payee', 'annulee'])
                ->groupBy('client_id')
                ->pluck('total', 'client_id');

        return Inertia::render('Portal/Compte/Dashboard', [
            'compte' => [
                'nom' => $compte->nom,
                'telephone' => $compte->telephone,
                'email' => $compte->email,
            ],
            'demandes' => $demandes,
            'clients' => $compte->liens->map(fn (LienCompteClient $lien) => [
                'id' => $lien->client->id,
                'code' => $lien->client->code,
                'nom' => $lien->client->nom,
                'statut' => $lien->client->statut,
                'factures_impayees' => (int) ($impayees[$lien->client_id] ?? 0),
                'verified_at' => $lien->verified_at?->toDateTimeString(),
            ]),
            'facturesRecentes' => $facturesRecentes->map(fn (Facture $facture) => [
                'id' => $facture->id,
                'numero' => $facture->numero,
                'montant' => $facture->montant,
                'statut' => $facture->statut,
                'due_date' => $facture->due_date?->toDateString(),
                'client' => [
                    'nom' => $facture->client->nom,
                    'code' => $facture->client->code,
                ],
            ]),
            'tickets' => $tickets->map(fn (SavTicket $ticket) => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'client_id' => $ticket->client_id,
                'created_at' => $ticket->created_at?->toDateTimeString(),
            ]),
        ]);
    }

    public function linkClient(Request $request): RedirectResponse
    {
        $compte = $this->portalCompte($request);

        $data = $request->validate([
            'code' => ['required', 'string', 'exists:clients,code'],
            'telephone' => ['required', 'string'],
        ]);

        $client = Client::where('code', $data['code'])->firstOrFail();

        if ($client->telephone !== $data['telephone']) {
            return back()->with('error', 'Le telephone ne correspond pas au dossier client.')->withInput();
        }

        LienCompteClient::updateOrCreate(
            ['compte_client_id' => $compte->id, 'client_id' => $client->id],
            [
                'statut' => 'actif',
                'verified_at' => now(),
                'last_confirmed_at' => now(),
            ]
        );

        Facture::where('client_id', $client->id)
            ->whereNull('compte_client_id')
            ->update(['compte_client_id' => $compte->id]);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Client lie avec succes.');
    }

    public function factures(Request $request): Response
    {
        $compte = $this->portalCompte($request, ['liens']);
        $clientIds = $this->linkedClientIds($compte);
        $statut = $request->query('statut');

        $facturesQuery = Facture::with('client')
            ->where(function (Builder $query) use ($compte, $clientIds) {
                $query->where('compte_client_id', $compte->id);

                if ($clientIds->isNotEmpty()) {
                    $query->orWhereIn('client_id', $clientIds);
                }
            });

        if ($statut) {
            $facturesQuery->where('statut', $statut);
        }

        $factures = $facturesQuery
            ->latest('due_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Facture $facture) => [
                'id' => $facture->id,
                'numero' => $facture->numero,
                'periode' => $facture->periode,
                'montant' => $facture->montant,
                'statut' => $facture->statut,
                'due_date' => $facture->due_date?->toDateString(),
                'paid_at' => $facture->paid_at?->toDateTimeString(),
                'client' => [
                    'nom' => $facture->client->nom,
                    'code' => $facture->client->code,
                ],
            ]);

        return Inertia::render('Portal/Compte/Factures/Index', [
            'factures' => $factures,
            'filters' => ['statut' => $statut],
            'statuts' => ['generee', 'envoyee', 'payee', 'annulee'],
        ]);
    }

    public function showFacture(Request $request, Facture $facture): Response
    {
        $this->authorizeFacture($request, $facture);

        $facture->loadMissing('client', 'paiements');

        return Inertia::render('Portal/Compte/Factures/Show', [
            'facture' => [
                'id' => $facture->id,
                'numero' => $facture->numero,
                'periode' => $facture->periode,
                'montant' => $facture->montant,
                'statut' => $facture->statut,
                'due_date' => $facture->due_date?->toDateString(),
                'issued_at' => $facture->issued_at?->toDateTimeString(),
                'paid_at' => $facture->paid_at?->toDateTimeString(),
                'pdf_available' => (bool) $facture->pdf_path,
                'client' => [
                    'nom' => $facture->client->nom,
                    'code' => $facture->client->code,
                    'telephone' => $facture->client->telephone,
                ],
            ],
            'paiements' => $facture->paiements->map(fn ($paiement) => [
                'id' => $paiement->id,
                'montant' => $paiement->montant,
                'mode' => $paiement->mode,
                'reference' => $paiement->reference,
                'paid_at' => $paiement->paid_at?->toDateTimeString(),
            ]),
        ]);
    }

    public function downloadFacture(Request $request, Facture $facture)
    {
        $this->authorizeFacture($request, $facture);

        if ($facture->pdf_path && Storage::disk('local')->exists($facture->pdf_path)) {
            return Storage::download($facture->pdf_path, $facture->numero.'.pdf');
        }

        $pdf = $this->renderFacturePdf($facture);

        return response()->streamDownload(
            fn () => print($pdf),
            $facture->numero.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function showProfile(Request $request): Response
    {
        $compte = $this->portalCompte($request);

        return Inertia::render('Portal/Compte/Profile', [
            'compte' => [
                'nom' => $compte->nom,
                'telephone' => $compte->telephone,
                'email' => $compte->email,
            ],
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $compte = $this->portalCompte($request);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $compte->nom = $data['nom'];
        $compte->email = $data['email'] ?? $compte->email;
        if (! empty($data['password'])) {
            $compte->password = Hash::make($data['password']);
        }
        $compte->save();

        return redirect()->route('portal.compte.dashboard')->with('success', 'Profil mis �� jour.');
    }

    public function showDemande(Request $request, Demande $demande): Response
    {
        $this->authorizeDemande($request, $demande);

        $timeline = [
            ['key' => 'soumise', 'label' => 'Soumise', 'description' => 'Votre demande est enregistrǸe.'],
            ['key' => 'en_etude', 'label' => 'En Ǹtude', 'description' => 'Nos Ǹquipes vǸrifient l�?TǸligibilitǸ.'],
            ['key' => 'planification', 'label' => 'Planification', 'description' => 'Nous planifions une visite ou une installation.'],
            ['key' => 'installee', 'label' => 'InstallǸe', 'description' => 'Le matǸriel est posǸ.'],
            ['key' => 'active', 'label' => 'Active', 'description' => 'Votre service est opǸrationnel.'],
        ];

        return Inertia::render('Portal/Compte/Demandes/Show', [
            'demande' => [
                'id' => $demande->id,
                'type' => $demande->type,
                'statut' => $demande->statut,
                'adresse' => $demande->adresse,
                'commentaire' => $demande->commentaire,
                'created_at' => $demande->created_at?->toDateTimeString(),
                'motif_annulation' => $demande->motif_annulation,
                'cancelled_at' => $demande->cancelled_at?->toDateTimeString(),
            ],
            'timeline' => $timeline,
            'canCancel' => in_array($demande->statut, ['soumise', 'en_etude', 'planification']),
        ]);
    }

    public function cancelDemande(Request $request, Demande $demande): RedirectResponse
    {
        $this->authorizeDemande($request, $demande);

        if (! in_array($demande->statut, ['soumise', 'en_etude', 'planification'])) {
            return back()->with('error', 'Cette demande ne peut plus Ǧtre annulǸe.');
        }

        $data = $request->validate([
            'motif' => ['required', 'string', 'max:500'],
        ]);

        $demande->update([
            'statut' => 'annulee',
            'motif_annulation' => $data['motif'],
            'cancelled_at' => now(),
        ]);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Demande annulǸe.');
    }

    public function storeSavTicket(Request $request): RedirectResponse
    {
        $compte = $this->portalCompte($request, ['liens']);
        $clientIds = $this->linkedClientIds($compte);

        $data = $request->validate([
            'client_id' => ['required', 'integer'],
            'type' => ['required', 'in:incident,assistance,reclamation'],
            'priority' => ['required', 'in:low,normal,high'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        if (! $clientIds->contains((int) $data['client_id'])) {
            abort(403);
        }

        SavTicket::create([
            'client_id' => (int) $data['client_id'],
            'type' => $data['type'],
            'channel' => 'portal',
            'priority' => $data['priority'],
            'status' => 'open',
            'subject' => $data['subject'],
            'description' => $data['description'],
        ]);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Ticket SAV soumis. Nos equipes vous contacteront.');
    }

    protected function authorizeDemande(Request $request, Demande $demande): void
    {
        if ($demande->compte_client_id !== $request->session()->get('portal_compte_id')) {
            abort(403);
        }
    }

    protected function authorizeFacture(Request $request, Facture $facture): void
    {
        $compte = $this->portalCompte($request, ['liens']);
        $clientIds = $this->linkedClientIds($compte);

        if ($facture->compte_client_id !== $compte->id && ! $clientIds->contains($facture->client_id)) {
            abort(403);
        }
    }

    protected function portalCompte(Request $request, array $with = []): CompteClient
    {
        $id = $request->session()->get('portal_compte_id');

        return CompteClient::with($with)->findOrFail($id);
    }

    protected function linkedClientIds(CompteClient $compte): Collection
    {
        $compte->loadMissing('liens');

        return $compte->liens->pluck('client_id')->filter()->unique();
    }

    protected function renderFacturePdf(Facture $facture): string
    {
        $facture->loadMissing('client');

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pdf.facture', ['facture' => $facture])->render();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }
}
