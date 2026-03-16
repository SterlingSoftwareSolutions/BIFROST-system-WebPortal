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
        Schema::table('daily_accessory', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });

        Schema::table('daily_conditionings', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });

        Schema::table('daily_strengths', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });

        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });

        Schema::table('daily_weightlifting', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });

        Schema::table('emom', function (Blueprint $table) {
            $table->text('exercise_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
        Schema::table('daily_accessory', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        });

        Schema::table('daily_conditionings', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        });

        /* Schema::table('daily_strengths', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        }); */

        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        });

        Schema::table('daily_weightlifting', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        });

        Schema::table('emom', function (Blueprint $table) {
            $table->dropColumn('exercise_time');
        });
    }
};
