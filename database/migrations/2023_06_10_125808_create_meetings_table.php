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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('subject');
            $table->string('location')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('start');
            $table->string('end');
            $table->json('participants')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
