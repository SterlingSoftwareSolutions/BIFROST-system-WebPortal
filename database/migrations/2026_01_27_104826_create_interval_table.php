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
        Schema::create('interval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_manager_id')->constrained('workout_manager')->cascadeOnDelete();
            $table->foreignId('workout_libraries_id')->constrained('workout_libraries')->cascadeOnDelete();
            $table->integer('training_load')->nullable();
            $table->enum('unit_type', ['%', 'Kg', 'Cal', 'RPE', 'BW', 'N/A'])->default('N/A');
            $table->time('work');
            $table->time('rest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interval');
    }
};
