<?php

namespace Database\Factories;

use App\Models\Bts;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bts>
 */
class BtsFactory extends Factory
{
    protected $model = Bts::class;

    public function definition(): array
    {
        $cities = [
            ['ville' => 'Douala', 'lat' => 4.05, 'lng' => 9.7],
            ['ville' => 'Yaoundé', 'lat' => 3.87, 'lng' => 11.52],
        ];
        $city = Arr::random($cities);
        $lat = $this->faker->randomFloat(7, $city['lat'] - 0.08, $city['lat'] + 0.08);
        $lng = $this->faker->randomFloat(7, $city['lng'] - 0.08, $city['lng'] + 0.08);

        return [
            'code' => 'BTS-' . Str::upper(Str::random(3)) . '-' . $this->faker->numberBetween(100, 999),
            'ville' => $city['ville'],
            'lat' => $lat,
            'lng' => $lng,
            'composants' => [
                'routeur' => $this->faker->randomElement(['MikroTik', 'Ubiquiti', 'Cambium', 'Huawei']),
                'antenne' => $this->faker->randomElement(['AirFiber', 'LiteBeam', 'Mimosa']),
                'backup' => $this->faker->randomElement(['Groupe électrogène', 'Onduleur']),
            ],
            'photos' => [],
        ];
    }
}
