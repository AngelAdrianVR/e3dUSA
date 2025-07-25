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
        Schema::create('programed_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('programed_by')->nullable(); //nombre del usuario que lo programó
            $table->timestamp('reminder_date')->nullable();
            $table->string('reminder_time')->nullable(); // hora del recordatorio
            $table->float('amount')->unsigned()->nullable(); // monto de la factura programada
            $table->unsignedTinyInteger('number_of_invoice')->nullable(); // Numero de factura (orden. Ej. 2 de 4)
            $table->unsignedTinyInteger('invoice_quantity')->nullable(); // Número de facturas relacionadas con esa ov
            $table->string('status')->default('Pendiente')->nullable(); // estatus del recordatorio (Pendiente, Creada)
            $table->float('total_amount_sale')->unsigned()->nullable(); // Monto total de la venta
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // usuario que lo programó
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programed_invoices');
    }
};
