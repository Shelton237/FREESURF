<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Client;
use App\Models\Bts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkOrderController extends Controller
{
    public function create(Request $request): Response
    {
        $client = Client::find($request->integer('client'));
        $techs = User::where('role', 'technicien')->orderBy('name')->get(['id','name','email']);
        return Inertia::render('Backoffice/WorkOrders/Create', [
            'client' => $client,
            'bts' => Bts::orderBy('ville')->get(['id','code','ville']),
            'technicians' => $techs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required','in:survey,install,maintenance'],
            'client_id' => ['nullable','exists:clients,id'],
            'bts_id' => ['nullable','exists:bts,id'],
            'assigned_to' => ['required','exists:users,id'],
            'scheduled_at' => ['nullable','date'],
            'notes' => ['nullable','string'],
        ]);
        $wo = WorkOrder::create($data);
        return redirect()->route('backoffice.clients.show', $data['client_id'] ?? $wo->client_id)->with('success', 'Intervention créée');
    }
}

