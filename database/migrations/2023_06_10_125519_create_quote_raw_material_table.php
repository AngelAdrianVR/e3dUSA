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
        Schema::create('quote_raw_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('quantity');
            $table->float('price');
            $table->boolean('show_image')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_quotes');
    }
};
