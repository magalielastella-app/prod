<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Métiers par défaut
        $defaults = [
            'Chirurgien-Dentiste',
            'Assistant(e) Dentaire',
            'Assistant(e) Administratif',
            'Office Manager',
            'Community Manager',
        ];
        foreach ($defaults as $name) {
            DB::table('job_positions')->insert([
                'name' => $name,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajouter une colonne job_position_id aux candidats
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('job_position_id')->nullable()->after('campaign_id')
                ->constrained('job_positions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_position_id');
        });
        Schema::dropIfExists('job_positions');
    }
};
