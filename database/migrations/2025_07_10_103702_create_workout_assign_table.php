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
        Schema::create('workout_assign', function (Blueprint $table) {
            $table->id();
             $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->unsignedBigInteger('workout_id');
            $table->string('workout_type');
            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_assign');
    }
};
