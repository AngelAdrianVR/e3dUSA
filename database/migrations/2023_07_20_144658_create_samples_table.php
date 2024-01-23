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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedFloat('quantity');
            $table->boolean('will_back')->default(false);
            $table->date('devolution_date')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('sale_order_at')->nullable();
            $table->text('comments')->nullable();
            $table->json('products')->nullable();
            $table->foreignId('catalog_product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('authorized_user_name')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
