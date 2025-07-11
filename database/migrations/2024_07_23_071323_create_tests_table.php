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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('category_options')->onDelete('cascade');
            $table->foreignId('workout_id')->nullable()->constrained('workout_libraries')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->string('workoutname')->nullable();
            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
