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
        Schema::create('design_authorizations', function (Blueprint $table) {
            $table->id();
            $table->string('version')->nullable();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('engrave_method')->nullable();
            $table->string('logistic')->nullable();
            $table->unsignedMediumInteger('quantity')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('design_accepted')->nullable();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('design_authorizations');
    }
};
