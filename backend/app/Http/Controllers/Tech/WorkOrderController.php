<?php

namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Installation;
use App\Models\SavTicket;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('statut');

        $query = WorkOrder::with('client', 'bts')
            ->where('assigned_to', $request->user()->id);

        if ($status === 'pending') {
            $query->whereIn('status', ['assigned', 'accepted']);
        } elseif ($status) {
            $query->where('status', $status);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = WorkOrder::selectRaw('status, COUNT(*) as total')
            ->where('assigned_to', $request->user()->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount = (int) ($statusCounts['assigned'] ?? 0) + (int) ($statusCounts['accepted'] ?? 0);

        return Inertia::render('Tech/Dashboard', [
            'items' => $items,
            'filters' => [
                'statut' => $status,
            ],
            'stats' => [
                'pending' => $pendingCount,
                'on_site' => (int) ($statusCounts['on_site'] ?? 0),
                'completed' => (int) ($statusCounts['completed'] ?? 0),
            ],
            'statusOptions' => ['pending', 'on_site', 'completed'],
        ]);
    }

    public function create(Request $request): Response
    {
        $filters = $request->only('search');
        $clients = Client::query()
            ->select('id', 'code', 'nom', 'telephone', 'statut')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('code', 'like', "%{$search}%")
                        ->orWhere('nom', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        return Inertia::render('Tech/WorkOrders/Create', [
            'clients' => $clients,
            'filters' => $filters,
            'types' => ['survey'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'type' => ['required', 'in:survey'],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $client = Client::findOrFail($data['client_id']);

        $workOrder = WorkOrder::create([
            'type' => $data['type'],
            'client_id' => $client->id,
            'bts_id' => $client->bts_id,
            'assigned_to' => $request->user()->id,
            'status' => 'assigned',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $workOrder->events()->create([
            'type' => 'status',
            'payload' => [
                'message' => 'Etude initiee depuis le terrain',
                'to' => 'assigned',
            ],
        ]);

        return redirect()
            ->route('tech.work-orders.show', $workOrder)
            ->with('success', 'Etude creee');
    }

    public function show(Request $request, WorkOrder $workOrder): Response
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $workOrder->load('client', 'bts', 'events', 'attachments');

        return Inertia::render('Tech/WorkOrders/Show', ['workOrder' => $workOrder]);
    }

    public function start(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);

        $data = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $workOrder->update([
            'status' => 'on_site',
            'started_at' => now(),
            'lat' => $data['lat'] ?? $workOrder->lat,
            'lng' => $data['lng'] ?? $workOrder->lng,
        ]);

        $workOrder->events()->create(['type' => 'status', 'payload' => ['to' => 'on_site']]);

        return back()->with('success', 'Intervention demarree');
    }

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);

        $rules = [
            'date' => ['nullable', 'date'],
            'commentaire' => ['nullable', 'string'],
        ];

        if ($workOrder->type === 'install') {
            $rules['date'][0] = 'required';
        }

        if ($workOrder->type === 'survey') {
            $rules['survey_result'] = ['required', 'in:available,not_available'];
            $rules['survey_reason'] = ['nullable', 'string'];
            $rules['survey_follow_up'] = ['nullable', 'boolean'];
        }

        $data = $request->validate($rules);

        $updatePayload = [
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $data['commentaire'] ?? null,
        ];

        if ($workOrder->type === 'survey') {
            $updatePayload['survey_result'] = $data['survey_result'];
            $updatePayload['survey_reason'] = $data['survey_reason'] ?? null;
            $updatePayload['survey_follow_up'] = (bool) ($data['survey_follow_up'] ?? false);
        }

        $workOrder->update($updatePayload);
        $workOrder->events()->create(['type' => 'status', 'payload' => ['to' => 'completed']]);

        if ($workOrder->type === 'install' && $workOrder->client_id) {
            $client = $workOrder->client;
            $client->refresh();
            $inst = $client->installation ?: new Installation(['client_id' => $client->id]);
            $inst->date = $data['date'] ?? now()->toDateString();
            $inst->commentaire = $data['commentaire'] ?? null;
            $inst->terminee = true;
            $inst->user_id = $request->user()->id;
            $inst->save();
            $client->statut = 'actif';
            $client->save();
        } elseif ($workOrder->type === 'survey' && $workOrder->client_id) {
            $client = $workOrder->client;
            if ($client) {
                $client->statut = $data['survey_result'] === 'available' ? 'eligible' : 'non_eligible';
                $client->save();
            }

            if (! empty($data['survey_follow_up'])) {
                $this->createFollowUpTicket($workOrder, $data['survey_reason'] ?? null);
            }
        }

        return back()->with('success', 'Intervention terminee');
    }

    public function uploadAttachment(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $path = $request->file('file')->store('work_orders/'.$workOrder->id, 'public');

        $workOrder->attachments()->create([
            'path' => $path,
            'mime' => $request->file('file')->getMimeType(),
            'meta' => ['size' => $request->file('file')->getSize()],
        ]);

        return back()->with('success', 'Fichier envoye');
    }

    protected function createFollowUpTicket(WorkOrder $workOrder, ?string $reason): void
    {
        if (! $workOrder->client_id) {
            return;
        }

        $exists = SavTicket::where('work_order_id', $workOrder->id)
            ->where('channel', 'tech')
            ->exists();

        if ($exists) {
            return;
        }

        SavTicket::create([
            'client_id' => $workOrder->client_id,
            'work_order_id' => $workOrder->id,
            'type' => 'assistance',
            'channel' => 'tech',
            'priority' => 'normal',
            'status' => 'open',
            'subject' => 'Suivi survey #'.$workOrder->id,
            'description' => $reason ?: 'Demande de suivi terrain',
        ]);

        $workOrder->events()->create([
            'type' => 'follow_up',
            'payload' => ['message' => 'Ticket SAV cree depuis le terrain'],
        ]);
    }
}
