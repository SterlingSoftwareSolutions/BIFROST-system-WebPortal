<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['straight', 'rounds', 'for_time', 'amrap', 'emom', 'interval', 'pyramid', 'circuit'];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `unit_type` ENUM('%', 'Kg', 'Cal', 'RPE', 'BW', 'N/A', 'm') DEFAULT 'N/A'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['straight', 'rounds', 'for_time', 'amrap', 'emom', 'interval', 'pyramid', 'circuit'];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `unit_type` ENUM('%', 'Kg', 'Cal', 'RPE', 'BW', 'N/A') DEFAULT 'N/A'");
        }
    }
};
