<?php

namespace Database\Seeders;

use App\Models\AnnualReview;
use App\Models\User;
use App\Support\Positions;
use App\Support\ReviewTemplate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Directrice d'exploitation (admin — voit tout, gère l'équipe)
        $director = User::firstOrCreate(
            ['email' => 'directrice@cabinet.fr'],
            [
                'name' => 'Claire Moreau',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'position' => Positions::OPERATIONS_DIRECTOR,
                'department' => 'Direction',
                'hired_on' => Carbon::now()->subYears(5)->toDateString(),
            ]
        );

        // Dentiste (manager de l'équipe soignante)
        $dentist = User::firstOrCreate(
            ['email' => 'dentiste@cabinet.fr'],
            [
                'name' => 'Dr Thomas Rivière',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAGER,
                'position' => Positions::DENTIST,
                'department' => 'Soins',
                'hired_on' => Carbon::now()->subYears(4)->toDateString(),
                'manager_id' => $director->id,
            ]
        );

        // Assistants (rattachés au dentiste)
        $dentalAssistants = [
            ['email' => 'amelie.assistante@cabinet.fr', 'name' => 'Amélie Garnier'],
            ['email' => 'karim.assistant@cabinet.fr', 'name' => 'Karim Benali'],
        ];
        foreach ($dentalAssistants as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_EMPLOYEE,
                    'position' => Positions::DENTAL_ASSISTANT,
                    'department' => 'Soins',
                    'hired_on' => Carbon::now()->subYears(2)->toDateString(),
                    'manager_id' => $dentist->id,
                ]
            );
        }

        // Assistant(e) administratif (rattaché à la directrice)
        User::firstOrCreate(
            ['email' => 'sophie.admin@cabinet.fr'],
            [
                'name' => 'Sophie Laurent',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::ADMIN_ASSISTANT,
                'department' => 'Administratif',
                'hired_on' => Carbon::now()->subYears(3)->toDateString(),
                'manager_id' => $director->id,
            ]
        );

        // Un entretien planifié pour chaque salarié pour l'année en cours
        $year = (int) Carbon::now()->year;
        $employees = User::where('role', User::ROLE_EMPLOYEE)->get();
        foreach ($employees as $employee) {
            AnnualReview::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => $year],
                [
                    'manager_id' => $employee->manager_id,
                    'scheduled_for' => Carbon::now()->addDays(14)->toDateString(),
                    'status' => AnnualReview::STATUS_SCHEDULED,
                    'template_key' => ReviewTemplate::keyForPosition($employee->position),
                ]
            );
        }
    }
}
