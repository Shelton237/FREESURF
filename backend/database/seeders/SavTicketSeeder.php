<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\SavTicket;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavTicketSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        if ($clients->isEmpty()) {
            return;
        }

        $assignees = User::where('role', 'backoffice')->get();
        if ($assignees->isEmpty()) {
            $assignees = User::factory()->count(2)->create(['role' => 'backoffice']);
        }

        SavTicket::factory()
            ->count(15)
            ->make()
            ->each(function (SavTicket $ticket) use ($clients, $assignees) {
                $client = $clients->random();
                $ticket->client_id = $client->id;
                $ticket->assigned_to = $assignees->random()->id;
                $ticket->save();
            });
    }
}
