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
        // Cadencier : liste de produits régulièrement commandés à un fournisseur,
        // avec référence, prix négocié et quantité habituelle. Chaque ligne peut
        // être liée à un produit de l'inventaire pour alimenter les stocks.
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();    // réf article chez le fournisseur
            $table->string('name');                      // dénomination sur le cadencier
            $table->string('unit', 20)->nullable();
            $table->decimal('pack_size', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('usual_quantity', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
