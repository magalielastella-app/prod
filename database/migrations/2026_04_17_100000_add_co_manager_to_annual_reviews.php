<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            // Personne qui assiste à l'entretien (co-évaluateur).
            // Purement informatif : aucun droit supplémentaire par rapport
            // à son rôle applicatif habituel.
            $table->foreignId('co_manager_id')
                ->nullable()
                ->after('manager_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('co_manager_id');
        });
    }
};
