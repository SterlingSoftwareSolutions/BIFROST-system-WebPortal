<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change the weight column in daily_warmups from decimal(8,2)
     * to varchar(255) to match daily_strengths, daily_weightlifting,
     * daily_conditionings, and daily_accessory tables.
     */
    public function up(): void
    {
        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->string('weight', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_warmups', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->change();
        });
    }
};
