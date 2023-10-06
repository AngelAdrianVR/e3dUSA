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
        Schema::create('oportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->unsignedFloat('amount')->nullable();
            $table->string('status')->default('Nueva');
            $table->string('priority');
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('estimated_finish_date')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oportunities');
    }
};
