<?php

namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
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
        $workOrder->load('client','bts','events','attachments');
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

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $rules = [
            'date' => ['nullable','date'],
            'commentaire' => ['nullable','string'],
        ];

        if ($workOrder->type === 'install') {
            $rules['date'][0] = 'required';
        }

        if ($workOrder->type === 'survey') {
            $rules['survey_result'] = ['required','in:available,not_available'];
            $rules['survey_reason'] = ['nullable','string'];
            $rules['survey_follow_up'] = ['nullable','boolean'];
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

        // Si c'est une installation, marquer le client actif
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
        }

        return back()->with('success', 'Intervention terminée');
    }

    public function uploadAttachment(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->assigned_to === $request->user()->id, 403);
        $data = $request->validate([
            'file' => ['required','file','mimes:jpg,jpeg,png,webp','max:8192'],
        ]);
        $path = $request->file('file')->store('work_orders/'.$workOrder->id, 'public');
        $workOrder->attachments()->create([
            'path' => $path,
            'mime' => $request->file('file')->getMimeType(),
            'meta' => ['size' => $request->file('file')->getSize()],
        ]);
        return back()->with('success','Fichier envoyé');
    }
}
