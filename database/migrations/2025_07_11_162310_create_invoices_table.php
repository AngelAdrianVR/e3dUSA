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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->timestamp('issue_date')->nullable(); // fecha de emision
            $table->float('total_amount_sale')->unsigned()->nullable(); // Monto total de la venta
            $table->float('invoice_amount')->unsigned()->nullable(); // Monto de esa factura (en caso de haber varias facturas para la misma orden se divide proporcionalmente)
            $table->string('currency')->nullable(); // Moneda
            $table->string('payment_method')->nullable(); // Método de pago (efectivo, transferencia, etc)
            $table->string('payment_option')->nullable(); // Método de pago (PUE - Pago en una sola exhibición, PPD - Pago en parcialidades o diferido)
            $table->unsignedTinyInteger('invoice_quantity')->nullable(); // Número de facturas relacionadas con esa ov
            $table->unsignedTinyInteger('number_of_invoice')->nullable(); // señala el numero de posicion de todas las facturas relacionadas a la misma venta. Ej. 2 de 3
            $table->string('status')->default('Pendiente de pago')->nullable(); // Estatus (pendiente de pago, Parcialmente pagada, cancelada)
            $table->json('complements')->nullable(); // complementos de esa factura
            $table->json('extra_invoices')->nullable();// informacion de facturas extra a esa venta
            $table->string('notes')->nullable();
            $table->string('created_by')->nullable(); //usuario que creó la factura
            $table->foreignId('company_branch_id')->constrained()->onDelete('cascade'); // Sucursal de la empresa
            $table->foreignId('sale_id')->constrained()->onDelete('cascade'); // Orden de venta relacionada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
