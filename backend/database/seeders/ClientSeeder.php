<?php

namespace Database\Seeders;

use App\Models\Bts;
use App\Models\Client;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $partners = Partner::all();
        if ($partners->isEmpty()) {
            $partners = Partner::factory()->count(4)->create();
        }

        $btsList = Bts::all();
        if ($btsList->isEmpty()) {
            $btsList = Bts::factory()->count(5)->create();
        }

        Client::factory()
            ->count(30)
            ->state(function () use ($partners, $btsList, $faker) {
                $bts = $btsList->random();
                return [
                    'partner_id' => $partners->random()->id,
                    'bts_id' => $bts->id,
                    'lat' => $faker->latitude($bts->lat - 0.08, $bts->lat + 0.08),
                    'lng' => $faker->longitude($bts->lng - 0.08, $bts->lng + 0.08),
                ];
            })
            ->create();
    }
}
