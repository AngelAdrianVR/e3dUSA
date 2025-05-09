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
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('part_number');
            $table->string('measure_unit');
            $table->unsignedSmallInteger('width')->nullable(); //dimension o medida del producto
            $table->unsignedSmallInteger('large')->nullable(); //dimension o medida del producto
            $table->unsignedSmallInteger('height')->nullable(); //dimension o medida del producto
            $table->unsignedSmallInteger('diameter')->nullable(); //dimension o medida del producto
            $table->unsignedFloat('cost')->nullable();
            $table->unsignedFloat('min_quantity')->nullable();
            $table->unsignedFloat('max_quantity')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_products');
    }
};
