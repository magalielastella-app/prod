<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder par défaut — volontairement vide.
 *
 * Le cabinet dispose de seeders dédiés non destructifs :
 *  - TeamSeeder           : import initial de l'équipe (idempotent)
 *  - TestAccountsSeeder   : comptes de test + entretiens de démo
 *  - ReviewTemplateSeeder : init des trames en base
 *
 * L'entretien de chaque salarié doit être planifié manuellement par
 * la directrice via l'onglet « Entretiens → Planifier ». On ne crée
 * RIEN automatiquement ici pour éviter de recréer des entretiens
 * supprimés à chaque redéploiement.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Volontairement vide — voir le docblock ci-dessus.
    }
}
