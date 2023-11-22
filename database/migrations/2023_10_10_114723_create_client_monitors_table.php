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
        Schema::create('client_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamp('date');
            $table->string('concept');
            $table->unsignedBigInteger('monitor_id');
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('oportunity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_monitors');
    }
};
