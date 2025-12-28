<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('statut', 'type', 'search');

        $demandes = Demande::with('client:id,code,nom')
            ->when($filters['statut'] ?? null, fn ($query, $statut) => $query->where('statut', $statut))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%")
                    ->orWhere('email_facturation', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Backoffice/Demandes/Index', [
            'demandes' => $demandes,
            'filters' => $filters,
            'options' => [
                'types' => ['abonnement', 'reabonnement'],
                'statuts' => ['soumise','en_etude','planification','validee','installee','close'],
            ],
        ]);
    }
}
