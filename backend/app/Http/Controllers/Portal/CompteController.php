<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CompteClient;
use App\Models\Demande;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'email' => ['nullable', 'email'],
        ]);

        $compte = CompteClient::where('telephone', $validated['telephone'])
            ->when($validated['email'] ?? null, fn ($query, $email) => $query->where('email', $email))
            ->first();

        if (! $compte) {
            return back()->with('error', 'Impossible de trouver un compte avec ces informations.');
        }

        $request->session()->put('portal_compte_id', $compte->id);

        return redirect()->route('portal.compte.dashboard')->with('success', 'Connexion réussie.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('portal_compte_id');

        return redirect()->route('portal.home')->with('success', 'Déconnecté.');
    }

    public function dashboard(Request $request): Response
    {
        $compteId = $request->session()->get('portal_compte_id');

        $compte = CompteClient::findOrFail($compteId);
        $demandes = Demande::where('compte_client_id', $compte->id)
            ->latest()
            ->get(['id','type','statut','created_at','adresse']);

        return Inertia::render('Portal/Compte/Dashboard', [
            'compte' => [
                'nom' => $compte->nom,
                'telephone' => $compte->telephone,
                'email' => $compte->email,
            ],
            'demandes' => $demandes,
        ]);
    }
}
