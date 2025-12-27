<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandeStoreRequest;
use App\Models\CompteClient;
use App\Models\Demande;
use Illuminate\Http\RedirectResponse;
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

        // Associer (ou créer) un compte client basique par téléphone
        $compte = CompteClient::firstOrCreate(
            ['telephone' => $data['telephone']],
            ['email' => $data['email_facturation'] ?? null, 'nom' => $data['nom']]
        );

        $data['compte_client_id'] = $compte->id;
        $data['statut'] = 'soumise';

        Demande::create($data);

        return redirect()->route('portal.home')->with('success', 'Demande envoyée. Vous serez contacté.');
    }
}

