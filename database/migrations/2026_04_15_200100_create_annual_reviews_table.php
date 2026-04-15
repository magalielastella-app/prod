<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->date('scheduled_for')->nullable();
            // scheduled -> employee_draft -> ready_for_manager -> manager_draft -> completed -> signed
            $table->string('status', 30)->default('scheduled');

            // Auto-évaluation salarié
            $table->text('self_achievements')->nullable();
            $table->text('self_difficulties')->nullable();
            $table->text('self_skills_developed')->nullable();
            $table->text('self_motivation')->nullable();

            // Bilan objectifs précédents
            $table->json('previous_objectives')->nullable();

            // Nouveaux objectifs
            $table->json('new_objectives')->nullable();

            // Développement / formation / mobilité
            $table->text('training_needs')->nullable();
            $table->text('career_development')->nullable();

            // Appréciation manager
            $table->text('manager_appreciation')->nullable();
            $table->text('manager_areas_for_improvement')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable(); // 1..5

            // Commentaires finaux
            $table->text('employee_comments')->nullable();
            $table->text('manager_comments')->nullable();

            // Signatures
            $table->timestamp('employee_signed_at')->nullable();
            $table->timestamp('manager_signed_at')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'year']);
            $table->index(['manager_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_reviews');
    }
};
