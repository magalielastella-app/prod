<?php

namespace Database\Seeders;

use App\Models\CleaningTask;
use App\Models\Employee;
use App\Models\Product;
use App\Models\TemperatureLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateur démo : demo@resto.fr / password
        User::firstOrCreate(
            ['email' => 'demo@resto.fr'],
            ['name' => 'Démo Restaurateur', 'password' => Hash::make('password')]
        );

        $today = Carbon::today();
        $in = fn (int $n) => (clone $today)->addDays($n)->toDateString();

        // Produits
        foreach ([
            ['name' => 'Tomates cerises', 'category' => 'Légumes', 'quantity' => 8, 'unit' => 'kg', 'min_threshold' => 5, 'expiration' => $in(4), 'price' => 3.50, 'supplier' => 'Rungis Primeurs'],
            ['name' => 'Filet de bœuf', 'category' => 'Viandes', 'quantity' => 2, 'unit' => 'kg', 'min_threshold' => 3, 'expiration' => $in(2), 'price' => 42.00, 'supplier' => 'Boucherie Martin'],
            ['name' => 'Saumon frais', 'category' => 'Poissons', 'quantity' => 4, 'unit' => 'kg', 'min_threshold' => 2, 'expiration' => $in(1), 'price' => 28.00, 'supplier' => 'Océan Primeur'],
            ['name' => 'Beurre doux', 'category' => 'Produits laitiers', 'quantity' => 12, 'unit' => 'kg', 'min_threshold' => 4, 'expiration' => $in(20), 'price' => 8.50, 'supplier' => 'Laiterie Normande'],
            ['name' => 'Farine T55', 'category' => 'Épicerie', 'quantity' => 25, 'unit' => 'kg', 'min_threshold' => 10, 'expiration' => $in(120), 'price' => 1.20, 'supplier' => 'Moulin Dupont'],
            ['name' => 'Vin rouge maison', 'category' => 'Boissons', 'quantity' => 18, 'unit' => 'btl', 'min_threshold' => 12, 'price' => 14.00, 'supplier' => 'Domaine Leclerc'],
        ] as $data) {
            Product::firstOrCreate(['name' => $data['name']], $data);
        }

        // Équipe
        foreach ([
            ['name' => 'Julie Durand', 'role' => 'Chef de cuisine', 'email' => 'julie@resto.fr'],
            ['name' => 'Marc Bernard', 'role' => 'Second', 'email' => 'marc@resto.fr'],
            ['name' => 'Sophie Leroy', 'role' => 'Serveuse', 'email' => 'sophie@resto.fr'],
            ['name' => 'Ahmed Khalil', 'role' => 'Plongeur', 'email' => 'ahmed@resto.fr'],
        ] as $data) {
            Employee::firstOrCreate(['email' => $data['email']], $data);
        }

        // Tâches de nettoyage
        foreach ([
            ['zone' => 'Plan de travail cuisine', 'frequency' => 'Quotidien', 'last_done' => $in(-1), 'agent' => 'Marc Bernard'],
            ['zone' => 'Hotte aspirante', 'frequency' => 'Hebdomadaire', 'last_done' => $in(-10), 'agent' => 'Ahmed Khalil'],
            ['zone' => 'Chambre froide', 'frequency' => 'Hebdomadaire', 'last_done' => $in(-5), 'agent' => 'Julie Durand'],
            ['zone' => 'Sols salle', 'frequency' => 'Quotidien', 'last_done' => null, 'agent' => 'Ahmed Khalil'],
            ['zone' => 'Vitrines', 'frequency' => 'Bi-mensuel', 'last_done' => $in(-20), 'agent' => 'Sophie Leroy'],
        ] as $data) {
            CleaningTask::firstOrCreate(['zone' => $data['zone']], $data);
        }

        // Relevés de température du jour
        foreach ([
            ['zone' => 'Réfrigérateur', 'temp' => 3.2, 'time' => '08:30'],
            ['zone' => 'Congélateur', 'temp' => -19.5, 'time' => '08:32'],
            ['zone' => 'Chambre froide viandes', 'temp' => 1.8, 'time' => '08:35'],
        ] as $data) {
            TemperatureLog::firstOrCreate(
                ['date' => $today, 'zone' => $data['zone']],
                array_merge($data, [
                    'date' => $today,
                    'agent' => 'Julie Durand',
                    'compliant' => TemperatureLog::isCompliant($data['zone'], $data['temp']),
                ])
            );
        }
    }
}
