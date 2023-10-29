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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('repeater')->nullable();
            $table->string('reminder')->nullable();
            $table->json('participants')->nullable();
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->boolean('is_full_day')->default(false);
            $table->string('status')->default('Pendiente');
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->string('start_time', 10)->nullable();
            $table->string('end_time', 10)->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
