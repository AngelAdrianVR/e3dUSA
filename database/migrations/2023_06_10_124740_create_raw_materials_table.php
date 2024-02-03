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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('material');
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('large')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('diameter')->nullable();
            $table->string('part_number');
            $table->text('description')->nullable();
            $table->string('measure_unit');
            $table->unsignedMediumInteger('min_quantity');
            $table->unsignedMediumInteger('max_quantity');
            $table->unsignedFloat('cost');
            $table->unsignedFloat('min_quantity_purchase')->nullable();
            $table->text('notes')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
