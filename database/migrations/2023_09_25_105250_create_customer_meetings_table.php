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
        Schema::create('customer_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('location');
            $table->date('date');
            $table->time('time');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_meetings');
    }
};
