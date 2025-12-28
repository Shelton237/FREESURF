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
    public function index(Request $request): Response
    {
        $filters = $request->only('status', 'type', 'search');

        $workOrders = WorkOrder::with(['client:id,code,nom', 'bts:id,code,ville', 'technician:id,name'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('client', fn ($q) => $q->where('code', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%"));
            })
            ->latest('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Backoffice/WorkOrders/Index', [
            'workOrders' => $workOrders,
            'filters' => $filters,
            'options' => [
                'statuses' => ['assigned','accepted','on_site','completed'],
                'types' => ['survey','install','maintenance'],
            ],
        ]);
    }

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
        if (! $data['bts_id'] && $data['client_id']) {
            $linkedClient = Client::find($data['client_id']);
            if ($linkedClient?->bts_id) {
                $data['bts_id'] = $linkedClient->bts_id;
            }
        }

        $wo = WorkOrder::create($data);

        return redirect()
            ->route('backoffice.clients.show', $data['client_id'] ?? $wo->client_id)
            ->with('success', 'Intervention créée');
    }

    public function surveys(): Response
    {
        $summary = WorkOrder::where('type', 'survey')
            ->selectRaw('bts_id, count(*) as total, sum(case when survey_result = ? then 1 else 0 end) as blocked, sum(case when survey_result = ? then 1 else 0 end) as ok', ['not_available', 'available'])
            ->groupBy('bts_id')
            ->with('bts:id,code,ville')
            ->orderByDesc('blocked')
            ->get()
            ->map(fn (WorkOrder $row) => [
                'bts' => $row->bts?->code ?? 'N/A',
                'ville' => $row->bts?->ville ?? '-',
                'total' => (int) $row->total,
                'blocked' => (int) $row->blocked,
                'ok' => (int) $row->ok,
                'bts_id' => $row->bts_id,
            ]);

        $recent = WorkOrder::where('type', 'survey')
            ->with(['client:id,nom,statut', 'bts:id,code,ville'])
            ->latest('completed_at')
            ->limit(25)
            ->get()
            ->map(fn (WorkOrder $wo) => [
                'id' => $wo->id,
                'client' => $wo->client ? [
                    'id' => $wo->client->id,
                    'nom' => $wo->client->nom,
                    'statut' => $wo->client->statut,
                ] : null,
                'bts' => $wo->bts?->code,
                'ville' => $wo->bts?->ville,
                'result' => $wo->survey_result ?? 'pending',
                'reason' => $wo->survey_reason,
                'follow_up' => (bool) $wo->survey_follow_up,
                'completed_at' => optional($wo->completed_at)->toDateTimeString(),
            ]);

        return Inertia::render('Backoffice/WorkOrders/Surveys', [
            'summary' => $summary,
            'recent' => $recent,
        ]);
    }
}
