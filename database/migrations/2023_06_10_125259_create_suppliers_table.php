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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nickname')->nullable(); // nombre con el que se le conoce al proveedor
            $table->string('address')->nullable();
            $table->string('post_code')->nullable();
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('banks')->nullable(); // cuentas bancarias a las que se paga el material comprado
            $table->string('phone');
            $table->json('raw_materials_id')->nullable(); // materia prima relacionada con el proveedor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
