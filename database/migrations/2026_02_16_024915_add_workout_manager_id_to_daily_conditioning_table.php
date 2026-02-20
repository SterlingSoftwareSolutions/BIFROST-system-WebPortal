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
            $table->unsignedBigInteger('workout_manager_id')->nullable()->after('conditioning_id');
            $table->foreign('workout_manager_id')->references('id')->on('workout_manager')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_conditionings', function (Blueprint $table) {
            $table->dropForeign(['workout_manager_id']);
            $table->dropColumn('workout_manager_id');
        });
    }
};
