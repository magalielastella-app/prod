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
        // Journal des mouvements de stock : entrées automatiques (réception
        // de facture fournisseur) ou sorties manuelles (consommation, perte...)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'in' | 'out'
            $table->decimal('quantity', 10, 2);
            $table->string('reason')->nullable(); // consommation, perte, vol, inventaire, facture...
            $table->string('source_type')->nullable(); // 'purchase_invoice' | 'manual'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('user')->nullable();
            $table->date('date');
            $table->timestamps();
            $table->index(['product_id', 'date']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
