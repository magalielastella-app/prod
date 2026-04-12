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
        // Informations générales de la société (singleton : une seule ligne).
        Schema::create('company_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Smash You');
            $table->string('legal_form')->nullable();     // SARL, SAS...
            $table->string('siret')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('rcs')->nullable();
            $table->string('ape_code')->nullable();
            $table->decimal('capital', 12, 2)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('opening_hours')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
