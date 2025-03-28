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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('quantity');
            $table->boolean('all_boxes_are_same')->default(false);
            $table->boolean('is_fragile')->default(false);
            $table->json('boxes')->nullable(); //id de caja, largo, ancho , alto, peso, cantidad ,costo
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
