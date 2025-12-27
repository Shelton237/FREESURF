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

