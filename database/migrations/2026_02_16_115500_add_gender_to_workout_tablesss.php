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
        $tables = ['rounds','amrap','emom','circuit','interval','pyramid','for_time'];

    foreach ($tables as $tableName) {
        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'gender')) {
            Schema::table($tableName, function (Blueprint $blueprint) {
                $blueprint->string('gender')->nullable()->after('unit_type');
            });
        }
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       $tables = ['rounds','amrap','emom','circuit','interval','pyramid','for_time'];

    foreach ($tables as $tableName) {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'gender')) {
            Schema::table($tableName, function (Blueprint $blueprint) {
                $blueprint->dropColumn('gender');
            });
        }
    }
    }
};
