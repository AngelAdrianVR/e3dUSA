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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('receiver');
            $table->string('department');
            $table->unsignedFloat('tooling_cost');
            $table->string('tooling_currency');
            $table->boolean('freight_cost_charged_in_product')->default(false);
            $table->string('freight_cost')->nullable();
            $table->string('freight_option')->default('Por cuenta del cliente');
            $table->boolean('tooling_cost_stroked')->default(0);
            $table->boolean('freight_cost_stroked')->default(0);
            $table->string('first_production_days');
            $table->text('notes')->nullable();
            $table->string('currency');
            $table->string('authorized_user_name')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->boolean('quote_acepted')->nullable();
            $table->string('rejected_razon')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('is_spanish_template')->default(true);
            $table->boolean('show_breakdown')->default(false);
            $table->json('approved_products')->nullable();
            $table->boolean('created_by_customer')->default(0)->nullable(); 
            $table->boolean('early_payment_discount')->default(0)->nullable(); // descuento por pago anticipado
            $table->timestamp('early_paid_at')->nullable(); // fecha de pago anticipado
            $table->float('discount')->unsigned(); // cantidad de descuento por pago anticipado
            $table->string('status')->default('No enviada'); // estatus de la cotizacion (No enviada, Recibida por el cliente, Aceptada, Rechazada)
            $table->foreignId('company_branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('prospect_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->cascadeOnDelete();
            // $table->foreignId('quote_id')->nullable()->constrained()->cascadeOnDelete(); // cotización relacionada a la ov
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
