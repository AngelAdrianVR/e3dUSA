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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->foreign('operator_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('catalog_product_company_sale_id');
            $table->text('tasks')->nullable();
            $table->unsignedTinyInteger('estimated_time_hours')->default(0);
            $table->unsignedTinyInteger('estimated_time_minutes')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedMediumInteger('scrap')->default(0);
            $table->text('scrap_reason')->nullable();
            $table->boolean('has_low_stock')->default(0);
            $table->boolean('is_paused')->default(0);
            $table->json('additionals')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
