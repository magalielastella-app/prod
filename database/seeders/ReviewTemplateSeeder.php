<?php

namespace Database\Seeders;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplates\AdminAssistantTemplate;
use App\Support\ReviewTemplates\AssistantTemplate;
use App\Support\ReviewTemplates\DentisteTemplate;
use App\Support\ReviewTemplates\DirectriceTemplate;
use Illuminate\Database\Seeder;

/**
 * Initialise la table review_templates à partir des 4 trames
 * définies en PHP. Idempotent (updateOrCreate).
 *
 * Exécution :  php artisan db:seed --class=ReviewTemplateSeeder --force
 */
class ReviewTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            AssistantTemplate::definition(),
            AdminAssistantTemplate::definition(),
            DirectriceTemplate::definition(),
            DentisteTemplate::definition(),
        ];

        foreach ($templates as $t) {
            ReviewTemplateModel::updateOrCreate(
                ['key' => $t['key']],
                [
                    'label' => $t['label'],
                    'header' => $t['header'] ?? [],
                    'sections' => $t['sections'],
                ]
            );
        }

        $this->command->info(count($templates) . ' trames importées dans review_templates.');
    }
}
