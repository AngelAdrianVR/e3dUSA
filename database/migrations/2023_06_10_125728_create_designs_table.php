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
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plans_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('company_branch_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('dimensions')->nullable();
            $table->unsignedTinyInteger('status');
            $table->text('design_data')->nullable();
            $table->text('specifications')->nullable();
            $table->string('pantones')->nullable();
            $table->unsignedBigInteger('designer_id');
            $table->foreign('designer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('design_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('original_design_id');
            $table->foreign('original_design_id')->references('id')->on('designs');
            $table->string('mesure_unit');
            $table->string('authorized_user_name')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('expected_end_at')->nullable();
            $table->boolean('is_complex')->default(false);
            $table->unsignedTinyInteger('reuse_percentage')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->json('design_modifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
