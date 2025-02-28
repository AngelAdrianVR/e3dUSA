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
            $table->string('oldest_updated_by')->nullable();
            $table->string('old_updated_by')->nullable();
            $table->string('new_updated_by')->nullable();
            $table->date('oldest_date')->nullable();
            $table->date('old_date')->nullable();
            $table->date('new_date');
            $table->unsignedFloat('oldest_price')->nullable();
            $table->unsignedFloat('old_price')->nullable();
            $table->unsignedFloat('new_price');
            $table->string('oldest_currency')->nullable();
            $table->string('old_currency')->nullable();
            $table->string('new_currency');
            $table->foreignId('catalog_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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
