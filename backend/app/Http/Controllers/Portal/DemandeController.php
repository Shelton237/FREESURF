<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandeStoreRequest;
use App\Models\Client;
use App\Models\CompteClient;
use App\Models\Demande;
use App\Models\LienCompteClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class DemandeController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Portal/Demandes/Create');
    }

    public function store(DemandeStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $password = $data['password'] ?? null;

        $compte = CompteClient::firstOrCreate(
            ['telephone' => $data['telephone']],
            [
                'email' => $data['email_facturation'] ?? null,
                'nom' => $data['nom'],
                'password' => Hash::make($password),
            ]
        );

        $compte->email = $data['email_facturation'] ?? $compte->email;
        $compte->nom = $data['nom'];
        if ($password) {
            $compte->password = Hash::make($password);
        }
        $compte->save();

        $client = null;
        if ($data['type'] === 'reabonnement') {
            $client = Client::where('code', $data['client_code'])->first();

            if (! $client) {
                return back()->with('error', 'Code client inconnu.')->withInput();
            }

            if ($client->telephone !== $data['telephone']) {
                return back()->with('error', 'Le telephone ne correspond pas au dossier client.')->withInput();
            }

            $hasUnpaid = $client->factures()->whereNotIn('statut', ['payee', 'annulee'])->exists();
            if ($hasUnpaid) {
                return back()->with('error', 'Des factures impayees existent pour ce client. Merci de les regler avant de demander un reabonnement.')->withInput();
            }

            $data['client_id'] = $client->id;
        }

        $data['compte_client_id'] = $compte->id;
        $data['statut'] = 'soumise';
        unset($data['password'], $data['password_confirmation'], $data['client_code']);

        Demande::create($data);

        if ($client) {
            LienCompteClient::firstOrCreate(
                [
                    'compte_client_id' => $compte->id,
                    'client_id' => $client->id,
                ],
                [
                    'statut' => 'actif',
                    'verified_at' => now(),
                    'last_confirmed_at' => now(),
                ]
            );
        }

        $request->session()->put('portal_compte_id', $compte->id);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Votre demande a ǸtǸ enregistrǸe.');
    }
}
