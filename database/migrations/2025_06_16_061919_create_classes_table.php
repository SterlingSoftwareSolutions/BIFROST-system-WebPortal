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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->time('time');
            $table->integer('duration');
            $table->integer('spots');
            $table->boolean('workout_assigned');
            $table->string('date');
            $table->boolean('is_warmup')->default(false);
            $table->boolean('is_strength')->default(false);
            $table->boolean('is_weightlifting')->default(false);
            $table->boolean('is_conditioning')->default(false);
            $table->boolean('is_test')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
