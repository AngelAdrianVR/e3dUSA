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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_name');
            $table->string('contact_charge');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('contact_phone_extension')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('status');
            $table->unsignedSmallInteger('branches_number');
            $table->text('abstract')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
