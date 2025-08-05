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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->default(0); //0:pendiente 1:Despachado 2:finalizado 3:recibido
            $table->text('notes')->nullable();
            $table->string('authorized_user_name')->nullable(); // nombre de quien autoriza. 
            $table->timestamp('authorized_at')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamp('emited_at')->nullable();
            $table->timestamp('recieved_at')->nullable();
            $table->boolean('is_iva_included')->default(false);
            $table->boolean('is_spanish_template')->default(true);
            $table->boolean('is_for_production')->default(true);
            $table->boolean('show_prices')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('products')->nullable();
            $table->json('bank_information')->nullable();
            $table->json('rating')->nullable();
            $table->string('carrier')->nullable();
            $table->string('invoice_folio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
