<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds the 'weight' column to daily_warmups to support saving
     * user-adjusted weights for straight-sets workouts in warmup category.
     */
    public function up(): void
    {
        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('reps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
