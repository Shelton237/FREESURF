<?php

namespace Database\Factories;

use App\Models\Demande;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Demande>
 */
class DemandeFactory extends Factory
{
    protected $model = Demande::class;

    public function definition(): array
    {
        return [
            'compte_client_id' => null,
            'client_id' => null,
            'type' => $this->faker->randomElement(['abonnement', 'reabonnement']),
            'statut' => $this->faker->randomElement(['soumise', 'en_etude', 'planification', 'validee']),
            'nom' => $this->faker->name(),
            'telephone' => $this->faker->phoneNumber(),
            'email_facturation' => $this->faker->safeEmail(),
            'adresse' => $this->faker->streetAddress(),
            'lat' => $this->faker->latitude(-4, 5),
            'lng' => $this->faker->longitude(10, 15),
            'commentaire' => $this->faker->sentence(),
        ];
    }
}
