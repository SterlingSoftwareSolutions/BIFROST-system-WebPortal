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
        Schema::create('weightliftings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('category_options')->onDelete('cascade');
            $table->foreignId('workout_id')->nullable()->constrained('workout_libraries')->onDelete('cascade');
            $table->string('workoutname')->nullable();
            $table->float('weight');
            $table->time('restredwe');
            $table->time('restyellowwe');
            $table->time('restgreenwe');

            $table->string('intensity');

            $table->foreignId('alt_category_id')->nullable()->constrained('category_options')->onDelete('cascade');
            $table->foreignId('alt_workout_id')->nullable()->constrained('workout_libraries')->onDelete('cascade');
            $table->string('alt_workoutname')->nullable();
            $table->float('alt_weight')->nullable();
            $table->time('altrestredwe')->nullable();
            $table->time('altrestyellowwe')->nullable();
            $table->time('altrestgreenwe')->nullable();
            $table->string('alt_intensity')->nullable();

            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weightliftings');
    }
};

