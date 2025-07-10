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
        Schema::create('strengths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('category_options')->onDelete('cascade');
            $table->foreignId('workout_id')->nullable()->constrained('workout_libraries')->onDelete('cascade');
            $table->string('workoutname')->nullable();
            $table->float('weight');
            $table->string('restred');
            $table->string('restyellow');
            $table->string('restgreen'); // Assuming rest is in seconds
            $table->string('intensity')->nullable(); // Adjust if you have a specific enum or validation
            $table->foreignId('alt_category_id')->nullable()->constrained('category_options')->onDelete('cascade');
            $table->foreignId('alt_workout_id')->nullable()->constrained('workout_libraries')->onDelete('cascade');
            $table->float('altweight')->nullable();
            $table->string('altrestred');
            $table->string('altrestyellow');
            $table->string('altrestgreen'); // Assuming rest is in seconds
            $table->string('altintensity')->nullable();
            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strengths');
    }
};
