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
        Schema::create('payroll_user', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('check_in');
            $table->time('start_break');
            $table->time('end_break');
            $table->time('check_out');
            $table->unsignedSmallInteger('late')->default(0);
            $table->boolean('extras_enabled')->default(0);
            $table->unsignedTinyInteger('extra_hours')->nullable();
            $table->unsignedTinyInteger('extra_minutes')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('justification_event_id')->constrained()->cascadeOnDelete();
            $table->json('additionals')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_user');
    }
};
