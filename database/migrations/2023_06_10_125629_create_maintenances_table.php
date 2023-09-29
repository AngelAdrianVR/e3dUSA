<?php

use App\Models\Maintenance;
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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->text('problems')->nullable();
            $table->text('actions');
            $table->unsignedFloat('cost');
            $table->enum('maintenance_type_id', [Maintenance::PREVENTIVE, Maintenance::CORRECTIVE]);
            $table->string('responsible');
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
