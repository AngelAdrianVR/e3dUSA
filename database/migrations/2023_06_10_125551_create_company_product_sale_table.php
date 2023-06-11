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
        Schema::create('company_product_sale', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('quantity');
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status');
            $table->json('assinged_jobs')->nullable();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_product_sale');
    }
};
