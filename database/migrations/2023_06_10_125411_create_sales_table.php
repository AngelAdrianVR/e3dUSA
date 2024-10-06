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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('shipping_company')->nullable();
            $table->unsignedFloat('freight_cost')->nullable();
            $table->string('oce_name')->nullable();
            $table->string('order_via')->nullable();
            $table->string('tracking_guide')->nullable();
            $table->string('is_sale_production')->default(1);
            $table->date('promise_date')->nullable();
            $table->string('invoice')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_high_priority')->default(false);
            $table->string('authorized_user_name')->nullable();
            $table->json('partialities')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('recieved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_by')->nullable();
            $table->string('status')->nullable();
            $table->string('shipping_type')->nullable(); //unica o parcialidades
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('oportunity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
