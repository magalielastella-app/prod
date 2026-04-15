<?php

namespace Database\Seeders;

use App\Models\AnnualReview;
use App\Models\CashSheet;
use App\Models\CleaningTask;
use App\Models\CompanyInfo;
use App\Models\Employee;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\TemperatureLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateur démo : demo@smashyou.fr / password (admin / manager)
        $demo = User::firstOrCreate(
            ['email' => 'demo@smashyou.fr'],
            [
                'name' => 'Équipe Smash You',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'position' => 'Gérant',
                'department' => 'Direction',
            ]
        );

        // Manager de démonstration : manager@smashyou.fr / password
        $manager = User::firstOrCreate(
            ['email' => 'manager@smashyou.fr'],
            [
                'name' => 'Léo Martin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAGER,
                'position' => 'Responsable de salle',
                'department' => 'Exploitation',
            ]
        );

        // Salariés de démonstration : julie@ / marc@ / sophie@ (password)
        $employeesSeed = [
            ['email' => 'julie@smashyou.fr', 'name' => 'Julie Durand', 'position' => 'Chef de cuisine', 'department' => 'Cuisine'],
            ['email' => 'marc@smashyou.fr', 'name' => 'Marc Bernard', 'position' => 'Grillardin', 'department' => 'Cuisine'],
            ['email' => 'sophie@smashyou.fr', 'name' => 'Sophie Leroy', 'position' => 'Serveuse', 'department' => 'Salle'],
        ];
        $reviewEmployees = [];
        foreach ($employeesSeed as $e) {
            $reviewEmployees[] = User::firstOrCreate(
                ['email' => $e['email']],
                [
                    'name' => $e['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_EMPLOYEE,
                    'position' => $e['position'],
                    'department' => $e['department'],
                    'manager_id' => $manager->id,
                    'hired_on' => Carbon::now()->subYears(2)->toDateString(),
                ]
            );
        }

        // Un entretien planifié par salarié pour l'année en cours
        foreach ($reviewEmployees as $emp) {
            AnnualReview::firstOrCreate(
                ['employee_id' => $emp->id, 'year' => (int) Carbon::now()->year],
                [
                    'manager_id' => $manager->id,
                    'scheduled_for' => Carbon::now()->addDays(14)->toDateString(),
                    'status' => AnnualReview::STATUS_SCHEDULED,
                ]
            );
        }

        // Société
        CompanyInfo::firstOrCreate([], [
            'name' => 'Smash You',
            'legal_form' => 'SAS',
            'siret' => '912 345 678 00015',
            'vat_number' => 'FR12 912345678',
            'rcs' => 'Paris B 912 345 678',
            'ape_code' => '5610A',
            'capital' => 10000,
            'address' => '12 rue des Gourmets',
            'postal_code' => '75011',
            'city' => 'Paris',
            'phone' => '01 23 45 67 89',
            'email' => 'contact@smashyou.fr',
            'website' => 'https://smashyou.fr',
            'manager_name' => 'Léo Martin',
            'opening_hours' => 'Lun-Sam 12h-14h30 & 19h-22h30',
        ]);

        $today = Carbon::today();
        $in = fn (int $n) => (clone $today)->addDays($n)->toDateString();

        // Produits
        $products = [
            ['name' => 'Pain à burger brioché', 'category' => 'Épicerie', 'quantity' => 80, 'unit' => 'pcs', 'min_threshold' => 40, 'expiration' => $in(4), 'price' => 0.35, 'supplier' => 'Boulangerie Nord'],
            ['name' => 'Steak haché 150g', 'category' => 'Viandes', 'quantity' => 60, 'unit' => 'pcs', 'min_threshold' => 40, 'expiration' => $in(2), 'price' => 1.80, 'supplier' => 'Boucherie Martin'],
            ['name' => 'Cheddar tranches', 'category' => 'Produits laitiers', 'quantity' => 5, 'unit' => 'kg', 'min_threshold' => 3, 'expiration' => $in(20), 'price' => 12.00, 'supplier' => 'Laiterie Normande'],
            ['name' => 'Pommes de terre (frites)', 'category' => 'Légumes', 'quantity' => 25, 'unit' => 'kg', 'min_threshold' => 15, 'expiration' => $in(10), 'price' => 1.50, 'supplier' => 'Rungis Primeurs'],
            ['name' => 'Tomates', 'category' => 'Légumes', 'quantity' => 4, 'unit' => 'kg', 'min_threshold' => 5, 'expiration' => $in(4), 'price' => 2.80, 'supplier' => 'Rungis Primeurs'],
            ['name' => 'Sauce burger maison', 'category' => 'Épicerie', 'quantity' => 12, 'unit' => 'L', 'min_threshold' => 6, 'price' => 5.20, 'supplier' => 'Épicerie du Chef'],
            ['name' => 'Coca-Cola 33cl', 'category' => 'Boissons', 'quantity' => 48, 'unit' => 'btl', 'min_threshold' => 24, 'price' => 1.00, 'supplier' => 'Distri-Boissons'],
            ['name' => 'Bacon fumé', 'category' => 'Viandes', 'quantity' => 2, 'unit' => 'kg', 'min_threshold' => 3, 'expiration' => $in(6), 'price' => 18.00, 'supplier' => 'Boucherie Martin'],
        ];
        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }

        // Fournisseurs
        $suppliers = [
            ['name' => 'Boucherie Martin', 'contact' => 'M. Martin', 'email' => 'contact@boucherie-martin.fr', 'phone' => '01 44 55 66 77', 'order_day' => 'Lundi, Jeudi'],
            ['name' => 'Rungis Primeurs', 'contact' => 'Sylvie', 'email' => 'commandes@rungis-primeurs.fr', 'phone' => '01 77 88 99 00', 'order_day' => 'Mardi, Vendredi'],
            ['name' => 'Laiterie Normande', 'email' => 'contact@laiterie-normande.fr', 'order_day' => 'Mercredi'],
            ['name' => 'Boulangerie Nord', 'email' => 'pains@boulangerie-nord.fr', 'order_day' => 'Tous les jours'],
            ['name' => 'Distri-Boissons', 'email' => 'ventes@distri-boissons.fr', 'order_day' => 'Mardi'],
            ['name' => 'Épicerie du Chef', 'order_day' => 'Vendredi'],
        ];
        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // Cadenciers (quelques lignes par fournisseur)
        $boucherie = Supplier::where('name', 'Boucherie Martin')->first();
        $steak = Product::where('name', 'Steak haché 150g')->first();
        $bacon = Product::where('name', 'Bacon fumé')->first();
        if ($boucherie && $boucherie->cadencier()->count() === 0) {
            $boucherie->cadencier()->createMany([
                ['product_id' => $steak->id, 'name' => 'Steak haché 150g - boîte x20', 'reference' => 'STK-150', 'unit' => 'pcs', 'pack_size' => 20, 'price' => 1.80, 'usual_quantity' => 100],
                ['product_id' => $bacon->id, 'name' => 'Bacon fumé tranché', 'reference' => 'BCN-01', 'unit' => 'kg', 'price' => 18.00, 'usual_quantity' => 5],
            ]);
        }
        $rungis = Supplier::where('name', 'Rungis Primeurs')->first();
        $patate = Product::where('name', 'Pommes de terre (frites)')->first();
        $tomato = Product::where('name', 'Tomates')->first();
        if ($rungis && $rungis->cadencier()->count() === 0) {
            $rungis->cadencier()->createMany([
                ['product_id' => $patate->id, 'name' => 'PDT frite Agria', 'reference' => 'PDT-FR', 'unit' => 'kg', 'pack_size' => 25, 'price' => 1.40, 'usual_quantity' => 50],
                ['product_id' => $tomato->id, 'name' => 'Tomates rondes cat.1', 'unit' => 'kg', 'pack_size' => 5, 'price' => 2.50, 'usual_quantity' => 10],
            ]);
        }

        // Équipe
        foreach ([
            ['name' => 'Julie Durand', 'role' => 'Chef de cuisine', 'email' => 'julie@smashyou.fr'],
            ['name' => 'Marc Bernard', 'role' => 'Grillardin', 'email' => 'marc@smashyou.fr'],
            ['name' => 'Sophie Leroy', 'role' => 'Serveuse', 'email' => 'sophie@smashyou.fr'],
            ['name' => 'Ahmed Khalil', 'role' => 'Plongeur', 'email' => 'ahmed@smashyou.fr'],
        ] as $data) {
            Employee::firstOrCreate(['email' => $data['email']], $data);
        }

        // Tâches de nettoyage
        foreach ([
            ['zone' => 'Plan de travail cuisine', 'frequency' => 'Quotidien', 'last_done' => $in(-1), 'agent' => 'Marc Bernard'],
            ['zone' => 'Plaque / Grill', 'frequency' => 'Quotidien', 'last_done' => $in(-1), 'agent' => 'Marc Bernard'],
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

        // Feuilles de caisse : 5 derniers jours ouvrés
        for ($i = 0; $i <= 6; $i++) {
            $d = (clone $today)->subDays($i)->toDateString();
            $ca = rand(1200, 2400);
            CashSheet::firstOrCreate(['date' => $d], [
                'date' => $d,
                'ca' => $ca,
                'ca_plateforme' => round($ca * 0.20, 2),
                'cb' => round($ca * 0.40, 2),
                'cb_sans_contact' => round($ca * 0.15, 2),
                'espece' => round($ca * 0.10, 2),
                'ticket_restaurant' => round($ca * 0.05, 2),
                'borne' => round($ca * 0.10, 2),
            ]);
        }
    }
}
