<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Positions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crée un compte de test par poste métier pour permettre à la directrice
 * de vérifier le comportement de l'application sous chaque profil.
 *
 * Tous les comptes ont le même mot de passe simple « Test1234 » et
 * must_change_password = false (pas de redirection forcée).
 *
 * Exécution :  php artisan db:seed --class=TestAccountsSeeder --force
 *
 * Pour supprimer les comptes de test plus tard, supprimer-les depuis
 * l'onglet "Équipe" ou exécuter :
 *   php artisan tinker --execute="App\Models\User::where('email', 'like', 'test.%@cabinetdentaireobiou.fr')->delete()"
 */
class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $magalie = User::where('email', 'gestion@cabinetdentaireobiou.fr')->first();
        $thibault = User::where('email', 'tandeol@hotmail.fr')->first();

        $password = Hash::make('Test1234');

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
            if ($user->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->command->info($created . ' compte(s) de test créé(s) / ' . count($testAccounts) . ' au total.');
        $this->command->info('Mot de passe commun : Test1234');
    }
}
