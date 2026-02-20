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
        Schema::table('daily_conditionings', function (Blueprint $table) {
            // Add polymorphic columns to identify which format table and ID
            $table->string('workout_format_type')->nullable()->after('workout_manager_id')
                ->comment('Format type: rounds, amrap, for_time, intervals, emom, straight_sets, circuits, pyramid');
            $table->unsignedBigInteger('workout_format_id')->nullable()->after('workout_format_type')
                ->comment('ID from the specific format table');
            
            // Add index for faster queries
            $table->index(['workout_format_type', 'workout_format_id'], 'workout_format_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('daily_conditionings', function (Blueprint $table) {
            $table->dropIndex('workout_format_index');
            $table->dropColumn(['workout_format_type', 'workout_format_id']);
        });
    }
};
