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
        Schema::create('straight_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_libraries_id')->constrained('workout_libraries')->cascadeOnDelete();
            $table->foreignId('straight_id')->constrained('straight')->cascadeOnDelete();
            $table->integer('res')->nullable();          // reps / result
            $table->integer('trainload')->nullable();    // training load value
            $table->enum('unittype', ['%', 'Kg', 'Cal', 'RPE', 'BW', 'N/A'])->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('straight_sets');
    }
};
