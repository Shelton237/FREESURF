<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\SavTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SavTicketController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('status', 'priority', 'search');

        $tickets = SavTicket::with(['client:id,code,nom', 'assignee:id,name'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('client', fn ($q) => $q->where('code', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $assignees = User::whereIn('role', ['backoffice', 'technicien'])
            ->orderBy('name')
            ->get(['id','name']);

        return Inertia::render('Backoffice/Sav/Index', [
            'tickets' => $tickets,
            'filters' => $filters,
            'options' => [
                'statuses' => ['open','in_progress','pending_client','resolved','closed'],
                'priorities' => ['low','normal','high'],
                'types' => ['incident','assistance','reclamation'],
                'channels' => ['phone','whatsapp','portal','email'],
            ],
            'assignees' => $assignees,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required','exists:clients,id'],
            'type' => ['required','in:incident,assistance,reclamation'],
            'channel' => ['required','in:phone,whatsapp,portal,email'],
            'priority' => ['required','in:low,normal,high'],
            'subject' => ['required','string','max:190'],
            'description' => ['required','string'],
            'assigned_to' => ['nullable','exists:users,id'],
        ]);

        SavTicket::create($data);

        return back()->with('success', 'Ticket SAV créé');
    }

    public function update(Request $request, SavTicket $savTicket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required','in:open,in_progress,pending_client,resolved,closed'],
            'assigned_to' => ['nullable','exists:users,id'],
            'priority' => ['required','in:low,normal,high'],
            'resolution_notes' => ['nullable','string'],
            'follow_up_at' => ['nullable','date'],
        ]);

        if (in_array($data['status'], ['resolved','closed'], true) && ! $savTicket->resolved_at) {
            $savTicket->resolved_at = now();
        }

        $savTicket->fill($data)->save();

        return back()->with('success', 'Ticket mis à jour');
    }
}
