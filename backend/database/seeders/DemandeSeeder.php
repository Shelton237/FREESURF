<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Demande;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DemandeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $clients = Client::all();

        if ($clients->isEmpty()) {
            return;
        }

        Demande::factory()
            ->count(12)
            ->state(function () use ($clients, $faker) {
                $attachToClient = $faker->boolean(70);
                $client = $attachToClient ? $clients->random() : null;

                return [
                    'client_id' => $client?->id,
                    'nom' => $client->nom ?? $faker->name(),
                    'telephone' => $client->telephone ?? $faker->phoneNumber(),
                    'email_facturation' => $client->email_facturation ?? $faker->safeEmail(),
                    'lat' => $client->lat ?? $faker->latitude(-4, 5),
                    'lng' => $client->lng ?? $faker->longitude(10, 15),
                ];
            })
            ->create();
    }
}
