<?php

namespace Database\Seeders;

use App\Models\AnnualReview;
use App\Models\User;
use App\Support\Positions;
use App\Support\ReviewTemplate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crée un compte de test par poste métier + un entretien planifié
 * pour chaque compte, pour permettre à la directrice de vérifier
 * le flux complet (auto-éval, manager, signature, PDF).
 *
 * Tous les comptes ont le même mot de passe simple « Test1234 » et
 * must_change_password = false (pas de redirection forcée).
 *
 * Exécution :  php artisan db:seed --class=TestAccountsSeeder --force
 */
class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $magalie = User::where('email', 'gestion@cabinetdentaireobiou.fr')->first();
        $thibault = User::where('email', 'tandeol@hotmail.fr')->first();

        $password = Hash::make('Test1234');

        //
        // 1) Créer les 7 comptes test (1 par poste)
        //
        $testAccounts = [
            [
                'email' => 'test.directrice@cabinetdentaireobiou.fr',
                'name' => 'Test Directrice',
                'role' => User::ROLE_ADMIN,
                'position' => Positions::OPERATIONS_DIRECTOR,
                'department' => 'Direction',
                'manager' => null,
            ],
            [
                'email' => 'test.dentiste@cabinetdentaireobiou.fr',
                'name' => 'Test Dentiste',
                'role' => User::ROLE_MANAGER,
                'position' => Positions::DENTIST,
                'department' => 'Soins',
                'manager' => null,
            ],
            [
                'email' => 'test.assistante.dentaire@cabinetdentaireobiou.fr',
                'name' => 'Test Assistante Dentaire',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::DENTAL_ASSISTANT,
                'department' => 'Soins',
                'manager' => $thibault,
            ],
            [
                'email' => 'test.assistante.administrative@cabinetdentaireobiou.fr',
                'name' => 'Test Assistante Administrative',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::ADMIN_ASSISTANT,
                'department' => 'Administratif',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.clinique@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Clinique',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::CLINICAL_REFERENT,
                'department' => 'Soins',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.administrative@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Administrative',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::ADMIN_REFERENT,
                'department' => 'Administratif',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.sterilisation@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Stérilisation',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::STERILIZATION_REFERENT,
                'department' => 'Soins',
                'manager' => $magalie,
            ],
        ];

        $created = 0;
        $users = [];
        foreach ($testAccounts as $a) {
            $user = User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name' => $a['name'],
                    'password' => $password,
                    'role' => $a['role'],
                    'position' => $a['position'],
                    'department' => $a['department'],
                    'manager_id' => $a['manager']?->id,
                    'must_change_password' => false,
                ]
            );
            $users[$a['email']] = $user;
            if ($user->wasRecentlyCreated) {
                $created++;
            }
        }

        //
        // 2) Créer un entretien planifié pour chaque compte test.
        //    Les 5 employés → manager = test.dentiste (celui qui conduit).
        //    Le test.dentiste → manager = test.directrice (sa propre review).
        //    La test.directrice → pas d'entretien (rien au-dessus d'elle).
        //
        $year = (int) Carbon::now()->year;
        $testDentiste = $users['test.dentiste@cabinetdentaireobiou.fr'] ?? null;
        $testDirectrice = $users['test.directrice@cabinetdentaireobiou.fr'] ?? null;

        $entretiensAssignments = [
            'test.assistante.dentaire@cabinetdentaireobiou.fr'      => $testDentiste,
            'test.assistante.administrative@cabinetdentaireobiou.fr' => $testDirectrice,
            'test.referente.clinique@cabinetdentaireobiou.fr'        => $testDentiste,
            'test.referente.administrative@cabinetdentaireobiou.fr'  => $testDirectrice,
            'test.referente.sterilisation@cabinetdentaireobiou.fr'   => $testDentiste,
            'test.dentiste@cabinetdentaireobiou.fr'                  => $testDirectrice,
        ];

        $reviewsCreated = 0;
        foreach ($entretiensAssignments as $email => $manager) {
            $employee = $users[$email] ?? null;
            if (! $employee || ! $manager || $employee->id === $manager->id) {
                continue;
            }
            $review = AnnualReview::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => $year],
                [
                    'manager_id' => $manager->id,
                    'scheduled_for' => Carbon::now()->addDays(7)->toDateString(),
                    'status' => AnnualReview::STATUS_SCHEDULED,
                    'template_key' => ReviewTemplate::keyForPosition($employee->position),
                ]
            );
            if ($review->wasRecentlyCreated) {
                $reviewsCreated++;
            }
        }

        $this->command->info($created . ' compte(s) de test créé(s) / ' . count($testAccounts) . ' au total.');
        $this->command->info($reviewsCreated . ' entretien(s) de test planifié(s).');
        $this->command->info('Mot de passe commun : Test1234');
    }
}
