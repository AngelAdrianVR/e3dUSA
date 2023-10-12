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
        Schema::create('payment_monitors', function (Blueprint $table) {
            $table->id();
            $table->timestamp('paid_at')->default(now());
            $table->unsignedFloat('amount');
            $table->string('payment_method');
            $table->string('concept');
            $table->text('notes')->nullable();
            $table->foreignId('oportunity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_monitors');
    }
};
