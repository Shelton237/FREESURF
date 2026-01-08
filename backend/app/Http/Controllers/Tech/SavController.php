<?php

namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\SavTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SavController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('statut');

        $query = SavTicket::with('client')
            ->where(function ($q) use ($request) {
                $q->where('assigned_to', $request->user()->id)
                    ->orWhereNull('assigned_to');
            });

        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Tech/Sav/Index', [
            'tickets' => $tickets,
            'filters' => ['statut' => $status],
            'statusOptions' => ['open', 'in_progress', 'pending_client', 'resolved', 'closed'],
        ]);
    }

    public function show(Request $request, SavTicket $savTicket): Response
    {
        $this->ensureOwnership($savTicket, $request);

        $savTicket->load('client', 'workOrder');

        return Inertia::render('Tech/Sav/Show', [
            'ticket' => [
                'id' => $savTicket->id,
                'subject' => $savTicket->subject,
                'description' => $savTicket->description,
                'status' => $savTicket->status,
                'priority' => $savTicket->priority,
                'type' => $savTicket->type,
                'channel' => $savTicket->channel,
                'resolution_notes' => $savTicket->resolution_notes,
                'follow_up_at' => $savTicket->follow_up_at?->toDateTimeString(),
                'resolved_at' => $savTicket->resolved_at?->toDateTimeString(),
                'client' => [
                    'id' => $savTicket->client?->id,
                    'nom' => $savTicket->client?->nom,
                    'telephone' => $savTicket->client?->telephone,
                    'code' => $savTicket->client?->code,
                ],
            ],
        ]);
    }

    public function update(Request $request, SavTicket $savTicket): RedirectResponse
    {
        $this->ensureOwnership($savTicket, $request);

        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,pending_client,resolved,closed'],
            'resolution_notes' => ['nullable', 'string'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $update = [
            'status' => $data['status'],
            'resolution_notes' => $data['resolution_notes'] ?? $savTicket->resolution_notes,
            'follow_up_at' => $data['follow_up_at'] ?? null,
        ];

        if ($data['status'] === 'resolved' && ! $savTicket->resolved_at) {
            $update['resolved_at'] = now();
        }

        $savTicket->update($update);

        return redirect()->route('tech.sav.show', $savTicket)->with('success', 'Ticket mis à jour.');
    }

    protected function ensureOwnership(SavTicket $savTicket, Request $request): void
    {
        if (! $savTicket->assigned_to) {
            $savTicket->update(['assigned_to' => $request->user()->id]);
        } elseif ($savTicket->assigned_to !== $request->user()->id) {
            abort(403);
        }
    }
}

