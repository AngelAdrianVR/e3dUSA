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
            $table->text('description')->nullable();
            $table->text('lost_oportunity_razon')->nullable();
            $table->json('tags')->nullable(); //se guardan varias etiquetas
            $table->unsignedTinyInteger('probability')->nullable(); // 1 al 100
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('estimated_finish_date')->nullable();
            $table->string('type_access_project');
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
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
