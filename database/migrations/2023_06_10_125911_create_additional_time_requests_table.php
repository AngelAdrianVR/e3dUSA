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
        Schema::create('additional_time_requests', function (Blueprint $table) {
            $table->id();
            $table->text('justification');
            $table->string('time_requested');
            $table->text('comments');
            $table->string('authorized_user_name');
            $table->timestamp('authorized_at')->nullable();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_time_requests');
    }
};
