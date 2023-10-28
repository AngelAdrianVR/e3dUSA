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
        Schema::create('whatsapp_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('contact_phone');
            $table->string('company_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('date')->nullable();
            $table->foreignId('oportunity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('client_monitor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_monitors');
    }
};
