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
        Schema::table('daily_warmups', function (Blueprint $table) {
            Schema::table('daily_warmups', function (Blueprint $table) {
            // Add a round_number column to store progress as "current/total"
            $table->string('round_number')->nullable()->after('workout_format_id')->comment('AMRAP progress stored as current/total, e.g., 1/3');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_warmups', function (Blueprint $table) {
            Schema::table('daily_warmups', function (Blueprint $table) {
            $table->dropColumn('round_number');
        });
        });
    }
};
