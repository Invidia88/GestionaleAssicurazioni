<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Gestionale',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $clients = Client::factory()->count(20)->create();

        $companies = collect([
            ['name' => 'Reale Mutua', 'contact_name' => 'Giulia Ferri'],
            ['name' => 'Allianz', 'contact_name' => 'Marco Conti'],
            ['name' => 'Generali Italia', 'contact_name' => 'Elena Riva'],
            ['name' => 'UnipolSai', 'contact_name' => 'Davide Moretti'],
            ['name' => 'Zurich Italia', 'contact_name' => 'Laura Galli'],
        ])->map(fn (array $company) => InsuranceCompany::factory()->create([
            ...$company,
            'contact_phone' => '+39 02 '.fake()->numerify('#######'),
            'contact_email' => str($company['name'])->lower()->replace(' ', '.')->append('@demo.test')->toString(),
        ]));

        $today = CarbonImmutable::today();
        $plans = [
            ['count' => 8, 'status' => 'scaduta', 'from' => -120, 'to' => -1],
            ['count' => 10, 'status' => 'in_scadenza', 'from' => 0, 'to' => 7],
            ['count' => 12, 'status' => 'in_scadenza', 'from' => 8, 'to' => 30],
            ['count' => 6, 'status' => 'attiva', 'from' => 31, 'to' => 365],
            ['count' => 4, 'status' => 'rinnovata', 'from' => 90, 'to' => 420],
        ];

        $counter = 1;

        foreach ($plans as $plan) {
            for ($i = 0; $i < $plan['count']; $i++) {
                $endDate = $today->addDays(fake()->numberBetween($plan['from'], $plan['to']));

                Policy::factory()->create([
                    'client_id' => $clients->random()->id,
                    'insurance_company_id' => $companies->random()->id,
                    'number' => 'POL-'.str_pad((string) $counter, 5, '0', STR_PAD_LEFT),
                    'start_date' => $endDate->subYear(),
                    'end_date' => $endDate,
                    'status' => $plan['status'],
                ]);

                $counter++;
            }
        }
    }
}
