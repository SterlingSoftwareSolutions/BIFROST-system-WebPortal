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
         Schema::create('daily_accessory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->unsignedBigInteger('workout_manager_id')->nullable();
            $table->foreign('workout_manager_id')->references('id')->on('workout_manager')->onDelete('cascade');
            $table->string('workout_format_type')->nullable()
                ->comment('Format type: rounds, amrap, for_time, intervals, emom, straight_sets, circuits, pyramid');
            $table->unsignedBigInteger('workout_format_id')->nullable()
                ->comment('ID from the specific format table');
            $table->index(['workout_format_type', 'workout_format_id'], 'workout_format_index');
            $table->integer('reps')->nullable();
            $table->string('weight')->nullable();
            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dailyAccessory', function (Blueprint $table) {
            //
        });
    }
};
