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
        Schema::create('client_managements', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->foreignId('workout')->nullable()->constrained('workout_libraries');
            $table->string('sets')->nullable();
            $table->string('reps')->nullable();
            $table->string('rest')->nullable();
            $table->string('intensity')->nullable();
            $table->string('date');
            $table->string('tab');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_managements');
    }
};
