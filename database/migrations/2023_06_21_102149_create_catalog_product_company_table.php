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
        Schema::create('catalog_product_company', function (Blueprint $table) {
            $table->id();
            $table->date('old_date')->nullable();
            $table->date('new_date');
            $table->unsignedMediumInteger('old_price')->nullable();
            $table->unsignedMediumInteger('new_price');
            $table->string('old_currency')->nullable();
            $table->string('new_currency');
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_product_company');
    }
};
