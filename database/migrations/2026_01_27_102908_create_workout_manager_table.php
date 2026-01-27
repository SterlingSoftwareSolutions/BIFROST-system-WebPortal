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
        Schema::create('workout_manager', function (Blueprint $table) {
            $table->id();
            $table->string('workout_name');
            $table->foreignId('type_id')->constrained('type')->cascadeOnDelete();
            $table->foreignId('format_id')->constrained('format')->cascadeOnDelete();
            $table->integer('number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_manager');
    }
};
