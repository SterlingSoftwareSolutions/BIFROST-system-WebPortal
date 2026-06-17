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
            $table->integer('set_number')->nullable()->after('workout_format_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_accessory', function (Blueprint $table) {
            $table->dropColumn('set_number');
        });
    }
};
