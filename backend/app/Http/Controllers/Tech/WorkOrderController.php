<?php

namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallationCompleteRequest;
use App\Models\Installation;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $items = WorkOrder::with('client','bts')
            ->where('assigned_to', $request->user()->id)
            ->latest()->paginate(10)->withQueryString();
        return Inertia::render('Tech/Dashboard', [ 'items' => $items ]);
    }

    public function show(Request $request, WorkOrder $workOrder): Response
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $workOrder->load('client','bts','events');
        return Inertia::render('Tech/WorkOrders/Show', [ 'workOrder' => $workOrder ]);
    }

    public function start(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $data = $request->validate([
            'lat' => ['nullable','numeric','between:-90,90'],
            'lng' => ['nullable','numeric','between:-180,180'],
        ]);
        $workOrder->update([
            'status' => 'on_site',
            'started_at' => now(),
            'lat' => $data['lat'] ?? $workOrder->lat,
            'lng' => $data['lng'] ?? $workOrder->lng,
        ]);
        $workOrder->events()->create(['type' => 'status', 'payload' => ['to' => 'on_site']]);
        return back()->with('success', 'Intervention démarrée');
    }

    public function complete(InstallationCompleteRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $workOrder->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $request->input('commentaire')
        ]);
        $workOrder->events()->create(['type' => 'status', 'payload' => ['to' => 'completed']]);

        // Si c'est une installation, marquer le client actif
        if ($workOrder->type === 'install' && $workOrder->client_id) {
            $client = $workOrder->client; $client->refresh();
            $inst = $client->installation ?: new Installation(['client_id' => $client->id]);
            $inst->date = $request->input('date') ?: now()->toDateString();
            $inst->commentaire = $request->input('commentaire');
            $inst->terminee = true; $inst->user_id = $request->user()->id; $inst->save();
            $client->statut = 'actif'; $client->save();
        }

        return back()->with('success', 'Intervention terminée');
    }
}

