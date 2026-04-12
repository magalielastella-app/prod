<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Feuille de caisse quotidienne : CA total + décomposition par mode
        // de règlement. Une seule feuille par date (unicité sur `date`).
        Schema::create('cash_sheets', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('ca', 10, 2)->default(0);
            $table->decimal('ca_plateforme', 10, 2)->default(0);
            $table->decimal('cb', 10, 2)->default(0);
            $table->decimal('cb_sans_contact', 10, 2)->default(0);
            $table->decimal('espece', 10, 2)->default(0);
            $table->decimal('ticket_restaurant', 10, 2)->default(0);
            $table->decimal('borne', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_sheets');
    }
};
