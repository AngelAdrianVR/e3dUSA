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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->unsignedFloat('weight');
            $table->unsignedFloat('width');
            $table->unsignedFloat('large');
            $table->unsignedFloat('height');
            $table->unsignedFloat('cost');
            $table->string('supplier');
            $table->date('aquisition_date');
            $table->unsignedSmallInteger('days_next_maintenance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
