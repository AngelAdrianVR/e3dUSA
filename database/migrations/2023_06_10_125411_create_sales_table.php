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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('shopping_company');
            $table->unsignedMediumInteger('freight_cost');
            $table->unsignedTinyInteger('status');
            $table->string('oce_name');
            $table->string('order_via');
            $table->string('tracking_guide');
            $table->text('notes')->nullable();
            $table->string('authorized_user_name');
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('recieved_at')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
