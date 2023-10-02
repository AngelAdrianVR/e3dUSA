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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('group');
            //$table->string('status'); // se calcula en en la vista
            $table->string('shipping_address')->nullable();
            $table->string('currency')->nullable();
            $table->string('sat_method')->nullable();
            $table->text('description');
            $table->boolean('is_strict_project')->default(false);
            $table->boolean('is_internal_project')->default(false);
            $table->unsignedFloat('budget')->nullable();
            $table->string('type_access_project');
            $table->date('start_date');
            $table->date('limit_date');
            $table->string('company_branch');
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete(); //ver si es viable tenerlo como id foraneo
            $table->foreignId('sale_id')->nullable()->constrained()->cascadeOnDelete(); //ver si es viable tenerlo como id foraneo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
