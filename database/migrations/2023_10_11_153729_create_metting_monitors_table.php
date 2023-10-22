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
        Schema::create('metting_monitors', function (Blueprint $table) {
            $table->id();
            $table->date('meeting_date');
            $table->time('time');
            $table->string('meeting_via');
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->text('description');
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('oportunity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_monitor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metting_monitors');
    }
};
