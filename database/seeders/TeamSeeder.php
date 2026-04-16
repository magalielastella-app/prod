<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Positions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Import de l'équipe réelle du Cabinet Dentaire de l'Obiou.
 *
 * Exécution :  php artisan db:seed --class=TeamSeeder --force
 *
 * Particularités :
 *  - Chaque compte reçoit un mot de passe temporaire unique.
 *  - must_change_password = true → la personne est forcée de choisir
 *    son propre mot de passe à la première connexion.
 *  - Le seeder est idempotent (firstOrCreate) : on peut le relancer
 *    sans écraser les comptes existants.
 *  - Les comptes de démo (directrice@cabinet.fr, etc.) sont supprimés
 *    en fin d'exécution.
 */
class TeamSeeder extends Seeder
{
    public function run(): void
    {
        //
        // 1) Directrice d'exploitation (admin)
        //
        $magalie = User::firstOrCreate(
            ['email' => 'gestion@cabinetdentaireobiou.fr'],
            [
                'name' => 'Magalie LASTELLA',
                'password' => Hash::make('Obiou7823'),
                'role' => User::ROLE_ADMIN,
                'position' => Positions::OPERATIONS_DIRECTOR,
                'department' => 'Direction',
                'must_change_password' => true,
            ]
        );

        //
        // 2) Dentistes / Chirurgiens-dentistes (role = manager).
        //    Les entretiens des dentistes sont conduits entre pairs ;
        //    leur user.manager_id reste null (ils sont au sommet de
        //    la hiérarchie, pas évalués par la directrice).
        //
        $dentistsSpec = [
            ['email' => 'tandeol@hotmail.fr',                'name' => 'Thibault ANDEOL',    'password' => 'Obiou9124'],
            ['email' => 'robin.basset@me.com',               'name' => 'Robin BASSET',       'password' => 'Obiou6405'],
            ['email' => 'drmeierthomas@gmail.com',           'name' => 'Thomas MEIER',       'password' => 'Obiou2748'],
            ['email' => 'dr.merindol@gmail.com',             'name' => 'Agathe MERINDOL',    'password' => 'Obiou8371'],
            ['email' => 'adaudeville@gmail.com',             'name' => 'Alice DAUDEVILLE',   'password' => 'Obiou4512'],
            ['email' => 'caro.donadieu@gmail.com',           'name' => 'Caroline DONADIEU',  'password' => 'Obiou8739'],
            ['email' => 'dr.rach.lei@gmail.com',             'name' => 'Leila RACHIDI',      'password' => 'Obiou3256'],
            ['email' => 'tifenn8@hotmail.fr',                'name' => 'Tifenn MANCHE',      'password' => 'Obiou6041'],
            ['email' => 'loic.fontanel@gmail.com',           'name' => 'Loic FONTANEL',      'password' => 'Obiou1387'],
            ['email' => 'dr.wiktoria.orysiak@gmail.com',     'name' => 'Wiktoria ORYSIAK',   'password' => 'Obiou7923'],
            ['email' => 'perrine.obstetar@hotmail.fr',       'name' => 'Perrine OBSTETAR',   'password' => 'Obiou5468'],
            ['email' => 'docteur.renecorail@gmail.com',      'name' => 'Diane RENE CORAIL',  'password' => 'Obiou9652'],
        ];

        $dentistByName = [];
        foreach ($dentistsSpec as $d) {
            $dentistByName[$d['name']] = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => Hash::make($d['password']),
                    'role' => User::ROLE_MANAGER,
                    'position' => Positions::DENTIST,
                    'department' => 'Soins',
                    'must_change_password' => true,
                ]
            );
        }

        //
        // 3) Assistantes et référentes (role = employee)
        //    Format : [email, name, position_key, manager_name, temp_password]
        //    position_key : 'dent' / 'admin' / 'ref_clinique' / 'ref_admin' / 'ref_steril'
        //
        $employees = [
            ['anae.baron28@gmail.com',        'Anaé BARON',            'admin',         'Magalie LASTELLA',  'Obiou2941'],
            ['laura.chaudet@hotmail.com',     'Laura CHAUDET',         'dent',          'Thibault ANDEOL',   'Obiou5037'],
            ['julie@delbar.fr',               'Julie DELBAR',          'dent',          'Robin BASSET',      'Obiou6128'],
            ['cloclotempesta@gmail.com',      'Chloé DIAFERIA',        'dent',          'Thomas MEIER',      'Obiou4216'],
            ['chgeeraert@gmail.com',          'Christel GIODDA',       'dent',          'Agathe MERINDOL',   'Obiou9384'],
            ['audreylambert.b@gmail.com',     'Audrey LAMBERT',        'ref_clinique',  'Magalie LASTELLA',  'Obiou1572'],
            ['veronique.larsen@laposte.net',  'Véronique LARSEN',      'admin',         'Magalie LASTELLA',  'Obiou8063'],
            ['julopes@hotmail.fr',            'Julie LOPES',           'admin',         'Magalie LASTELLA',  'Obiou3719'],
            ['taoutaoulinda@gmail.com',       'Linda MAKHLOUCHE',      'dent',          'Agathe MERINDOL',   'Obiou6842'],
            ['echelard.e@gmail.com',          'Elisa MARCHISIO',       'admin',         'Magalie LASTELLA',  'Obiou4591'],
            ['lauriemasnada@hotmail.com',     'Laure MASNADA',         'dent',          'Robin BASSET',      'Obiou2376'],
            ['fmazzilli9@icloud.com',         'Fiona MAZZILLI',        'dent',          'Thomas MEIER',      'Obiou7051'],
            ['severine.de-palma@orange.fr',   'Severine MULERO',       'ref_admin',     'Magalie LASTELLA',  'Obiou5284'],
            ['cristianoanea@yahoo.com',       'Cristian OANEA',        'dent',          'Thibault ANDEOL',   'Obiou9617'],
            ['palamuso.marine91@gmail.com',   'Marine PALAMUSO',       'dent',          'Robin BASSET',      'Obiou3462'],
            ['melissa.ptrtp@gmail.com',       'Melissa PATIR',         'admin',         'Magalie LASTELLA',  'Obiou8190'],
            ['lolprost@gmail.com',            'Laurence PROST',        'dent',          'Robin BASSET',      'Obiou5743'],
            ['claudiarivasr85@gmail.com',     'Claudia RIVAS',         'dent',          'Thibault ANDEOL',   'Obiou2905'],
            ['charlotterocahague@yahoo.fr',   'Charlotte ROCA-HAGUE',  'dent',          'Thibault ANDEOL',   'Obiou6831'],
            ['sarasara38400@gmail.com',       'Sara ROCCHI',           'dent',          'Agathe MERINDOL',   'Obiou4057'],
            ['sarahpatu@hotmail.fr',          'Sarah SAUMON',          'ref_steril',    'Magalie LASTELLA',  'Obiou7629'],
            ['trapieremma@outlook.fr',        'Emma TRAPIER',          'dent',          'Thomas MEIER',      'Obiou1843'],
            ['clara.vazm@outlook.fr',         'Clara VAZ MARQUES',     'admin',         'Magalie LASTELLA',  'Obiou5076'],
            ['berra.gyorur@gmail.com',        'Berra YORUR',           'dent',          'Thomas MEIER',      'Obiou3598'],
        ];

        $created = 0;
        $skipped = 0;
        foreach ($employees as [$email, $name, $posKey, $managerName, $password]) {
            $position = match ($posKey) {
                'admin' => Positions::ADMIN_ASSISTANT,
                'dent' => Positions::DENTAL_ASSISTANT,
                'ref_clinique' => Positions::CLINICAL_REFERENT,
                'ref_admin' => Positions::ADMIN_REFERENT,
                'ref_steril' => Positions::STERILIZATION_REFERENT,
                default => Positions::DENTAL_ASSISTANT,
            };
            $manager = $managerName === 'Magalie LASTELLA'
                ? $magalie
                : ($dentistByName[$managerName] ?? null);

            if (! $manager) {
                $this->command->warn("  Manager introuvable pour {$name} : {$managerName}");
                $skipped++;
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => User::ROLE_EMPLOYEE,
                    'position' => $position,
                    'department' => in_array($posKey, ['admin', 'ref_admin'], true) ? 'Administratif' : 'Soins',
                    'manager_id' => $manager->id,
                    'must_change_password' => true,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        //
        // 4) Nettoyage des comptes de démonstration et des anciens
        //    placeholders @cabinetdentaireobiou.fr créés avant la
        //    réception de la liste réelle des dentistes.
        //
        $obsoleteEmails = [
            // démo initiale
            'directrice@cabinet.fr',
            'dentiste@cabinet.fr',
            'amelie.assistante@cabinet.fr',
            'karim.assistant@cabinet.fr',
            'sophie.admin@cabinet.fr',
            'demo@smashyou.fr',
            // placeholders dentistes (avant réception des vrais emails)
            'thibault.andeol@cabinetdentaireobiou.fr',
            'robin.basset@cabinetdentaireobiou.fr',
            'thomas.meier@cabinetdentaireobiou.fr',
            'agathe.merindol@cabinetdentaireobiou.fr',
        ];
        $deleted = User::whereIn('email', $obsoleteEmails)->delete();

        $this->command->info("TeamSeeder terminé :");
        $this->command->info("  • 1 directrice · " . count($dentistsSpec) . " dentistes · {$created} salarié(e)s créé(e)s (" . ($skipped ? $skipped . ' existait(ent) déjà' : 'tous nouveaux') . ")");
        if ($deleted > 0) {
            $this->command->info("  • {$deleted} compte(s) obsolète(s) supprimé(s)");
        }
    }
}
