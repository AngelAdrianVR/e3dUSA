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
        Schema::create('catalog_product_company_sale', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('quantity');
            $table->unsignedFloat('finished_product_used', 10, 2);
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->json('assigned_jobs')->nullable();
            $table->unsignedBigInteger('catalog_product_company_id');
            $table->unsignedBigInteger('sale_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_product_company_sale');
    }
};
