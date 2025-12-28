<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin/backoffice
        User::updateOrCreate(
            ['email' => 'admin@freesurf.local'],
            [
                'name' => 'Admin Backoffice',
                'password' => Hash::make('Open2025!'),
                'role' => 'backoffice',
            ]
        );

        // Technicien
        User::updateOrCreate(
            ['email' => 'tech@freesurf.local'],
            [
                'name' => 'Tech One',
                'password' => Hash::make('Open2025!'),
                'role' => 'technicien',
            ]
        );

        $this->call([
            PartnerSeeder::class,
            BtsSeeder::class,
            ClientSeeder::class,
            DemandeSeeder::class,
            SavTicketSeeder::class,
            WorkOrderSeeder::class,
        ]);
    }
}
