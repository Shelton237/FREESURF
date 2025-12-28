<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Partner;
use App\Models\Bts;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['domicile', 'entreprise']);
        $statuts = ['prospect', 'eligible', 'non_eligible', 'installe', 'actif', 'suspendu'];

        return [
            'code' => 'CLI-' . Str::upper(Str::random(4)) . '-' . $this->faker->numberBetween(1000, 9999),
            'nom' => $this->faker->name(),
            'telephone' => $this->faker->unique()->e164PhoneNumber(),
            'type' => $type,
            'email_facturation' => $type === 'entreprise' ? $this->faker->companyEmail() : null,
            'lat' => $this->faker->latitude(-4, 5),
            'lng' => $this->faker->longitude(10, 15),
            'photos' => [],
            'partner_id' => Partner::factory(),
            'bts_id' => Bts::factory(),
            'statut' => $this->faker->randomElement($statuts),
        ];
    }
}
