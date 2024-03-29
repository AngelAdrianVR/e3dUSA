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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password'); // se utiliza para ingresar a la app de clientes
            $table->text('address');
            $table->string('state')->nullable();
            $table->string('post_code');
            $table->string('meet_way')->nullable();
            $table->string('sat_method');
            $table->string('sat_type');
            $table->string('sat_way');
            $table->text('important_notes')->nullable();
            $table->unsignedSmallInteger('days_to_reactivate')->default(0);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
