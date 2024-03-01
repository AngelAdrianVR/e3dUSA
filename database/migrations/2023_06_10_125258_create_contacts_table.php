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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->morphs('contactable');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->unsignedTinyInteger('birthdate_day')->nullable();
            $table->unsignedTinyInteger('birthdate_month')->nullable();
            $table->string('charge')->nullable();
            $table->json('additional_emails')->nullable();
            $table->json('additional_phones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
