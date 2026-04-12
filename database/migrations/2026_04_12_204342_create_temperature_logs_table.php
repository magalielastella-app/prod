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
        Schema::create('temperature_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('zone');
            $table->decimal('temp', 5, 1);
            $table->time('time');
            $table->boolean('compliant')->default(true);
            $table->string('agent')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperature_logs');
    }
};
