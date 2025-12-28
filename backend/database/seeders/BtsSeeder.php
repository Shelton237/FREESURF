<?php

namespace Database\Seeders;

use App\Models\Bts;
use Illuminate\Database\Seeder;

class BtsSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Douala', 'lat' => 4.05, 'lng' => 9.7, 'count' => 4],
            ['name' => 'Yaoundé', 'lat' => 3.87, 'lng' => 11.52, 'count' => 4],
        ];

        foreach ($cities as $city) {
            Bts::factory()
                ->count($city['count'])
                ->state(function () use ($city) {
                    $faker = fake();
                    return [
                        'ville' => $city['name'],
                        'lat' => $faker->randomFloat(7, $city['lat'] - 0.08, $city['lat'] + 0.08),
                        'lng' => $faker->randomFloat(7, $city['lng'] - 0.08, $city['lng'] + 0.08),
                    ];
                })
                ->create();
        }
    }
}
