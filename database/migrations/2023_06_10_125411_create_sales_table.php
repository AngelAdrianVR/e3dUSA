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
            // $table->string('shipping_company')->nullable();
            // $table->string('tracking_guide')->nullable();
            // $table->date('promise_date')->nullable();
            $table->unsignedFloat('freight_cost')->nullable();
            $table->boolean('freight_cost_charged_in_product')->default(false);
            $table->string('oce_name')->nullable();
            $table->string('order_via')->nullable();
            $table->string('is_sale_production')->default(1);
            $table->string('invoice')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_high_priority')->default(false);
            $table->string('authorized_user_name')->nullable();
            $table->json('partialities')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('recieved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_by')->nullable();
            $table->string('status')->default('Esperando autorización');
            $table->string('shipping_type')->nullable(); //unica o parcialidades
            $table->string('shipping_option')->nullable();
            $table->string('freight_option')->nullable(); //costo normal cargado al cliente, Cargo del flete en precio del producto,Emblems3d absorbe el costo del flete...
            $table->float('total_amount')->unsigned()->nullable(); // Costo total de la venta con iva
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
