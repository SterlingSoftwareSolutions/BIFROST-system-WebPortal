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
        Schema::create('warmups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()
                ->constrained('category_options')
                ->onDelete('cascade');
            $table->foreignId('workout_id')->nullable()
                ->constrained('workout_libraries')
                ->onDelete('cascade');
            $table->string('workoutname')->nullable();
            $table->integer('reps');
            $table->string('date');
            $table->float('weight')->nullable();
            $table->enum('unit', ['%', 'Kg', 'Cal', '/10']);
            $table->float('male')->nullable();
            $table->float('female')->nullable();
            $table->boolean('is_assigned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warmup');
    }
};
