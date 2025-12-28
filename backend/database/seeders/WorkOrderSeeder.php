<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::whereNotNull('bts_id')->get();

        if ($clients->isEmpty()) {
            return;
        }

        $technician = User::where('role', 'technicien')->first();

        if (! $technician) {
            $technician = User::factory()->create([
                'name' => 'Tech Seeder',
                'email' => 'tech+' . uniqid() . '@freesurf.local',
                'password' => bcrypt('Open2025!'),
                'role' => 'technicien',
            ]);
        }

        $count = min(12, $clients->count());
        $statuses = ['assigned', 'accepted', 'on_site', 'completed'];

        collect(range(1, $count))->each(function () use ($clients, $technician, $statuses) {
            $client = $clients->random();

            WorkOrder::factory()->create([
                'client_id' => $client->id,
                'bts_id' => $client->bts_id,
                'assigned_to' => $technician->id,
                'status' => Arr::random($statuses),
                'lat' => $client->lat,
                'lng' => $client->lng,
            ]);
        });
    }
}
