<?php

namespace Database\Factories;

use App\Models\SavTicket;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavTicket>
 */
class SavTicketFactory extends Factory
{
    protected $model = SavTicket::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['open', 'in_progress', 'pending_client', 'resolved', 'closed']);
        $resolved = in_array($status, ['resolved', 'closed'], true) ? $this->faker->dateTimeBetween('-10 days', 'now') : null;

        return [
            'client_id' => Client::factory(),
            'assigned_to' => User::factory(),
            'type' => $this->faker->randomElement(['incident', 'assistance', 'reclamation']),
            'channel' => $this->faker->randomElement(['phone', 'whatsapp', 'portal']),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high']),
            'status' => $status,
            'subject' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'resolution_notes' => $resolved ? $this->faker->sentence(10) : null,
            'follow_up_at' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('now', '+7 days') : null,
            'resolved_at' => $resolved,
        ];
    }
}
