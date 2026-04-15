<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute les colonnes "template"
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->string('template_key', 50)->default('assistant')->after('status');
            $table->json('header')->nullable()->after('template_key');
            $table->json('employee_answers')->nullable()->after('header');
            $table->json('manager_answers')->nullable()->after('employee_answers');
        });

        // Supprime les colonnes "texte libre" de l'ancienne version
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'self_achievements',
                'self_difficulties',
                'self_skills_developed',
                'self_motivation',
                'previous_objectives',
                'new_objectives',
                'training_needs',
                'career_development',
                'manager_appreciation',
                'manager_areas_for_improvement',
                'overall_rating',
                'employee_comments',
                'manager_comments',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropColumn(['template_key', 'header', 'employee_answers', 'manager_answers']);

            $table->text('self_achievements')->nullable();
            $table->text('self_difficulties')->nullable();
            $table->text('self_skills_developed')->nullable();
            $table->text('self_motivation')->nullable();
            $table->json('previous_objectives')->nullable();
            $table->json('new_objectives')->nullable();
            $table->text('training_needs')->nullable();
            $table->text('career_development')->nullable();
            $table->text('manager_appreciation')->nullable();
            $table->text('manager_areas_for_improvement')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable();
            $table->text('employee_comments')->nullable();
            $table->text('manager_comments')->nullable();
        });
    }
};
