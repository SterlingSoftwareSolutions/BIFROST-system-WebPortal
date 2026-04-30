<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'daily_warmups',
            'daily_strengths',
            'daily_weightlifting',
            'daily_conditionings',
            'daily_accessory'
        ];

        foreach ($tables as $tableName) {
            // Add notes column if it doesn't exist
            if (!Schema::hasColumn($tableName, 'notes')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('notes')->nullable();
                });
            }

            // Change round_number to string using raw SQL for maximum compatibility
            // This avoids the need for doctrine/dbal and works directly with MySQL
            if (Schema::hasColumn($tableName, 'round_number')) {
                try {
                    DB::statement("ALTER TABLE `$tableName` MODIFY COLUMN `round_number` VARCHAR(255) NULL");
                    echo "Successfully modified round_number in $tableName\n";
                } catch (\Exception $e) {
                    echo "Error modifying round_number in $tableName: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'daily_warmups',
            'daily_strengths',
            'daily_weightlifting',
            'daily_conditionings',
            'daily_accessory'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'round_number')) {
                DB::statement("ALTER TABLE `$tableName` MODIFY COLUMN `round_number` INT NULL");
            }
            if (Schema::hasColumn($tableName, 'notes')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('notes');
                });
            }
        }
    }
};
