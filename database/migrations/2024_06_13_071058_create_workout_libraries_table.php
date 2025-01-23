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
        Schema::create('workout_libraries', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('category_options_id'); // Foreign key to category_options table
            $table->string('type'); // Type of the workout
            $table->string('workout'); // Workout description or name
            $table->string('link')->nullable(); // Link to the workout resource
            $table->timestamps();
            // Define the foreign key constraint
            $table->foreign('category_options_id')->references('id')->on('category_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_libraries');
    }
};
