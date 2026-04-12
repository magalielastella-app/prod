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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('supplier');
            $table->string('product');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20)->nullable();
            $table->decimal('temp_delivery', 5, 1)->nullable();
            $table->date('dlc')->nullable();
            $table->boolean('compliant')->default(true);
            $table->timestamps();
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
