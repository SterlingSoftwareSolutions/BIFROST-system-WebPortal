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
        Schema::table('tests', function (Blueprint $table) {
            $table->foreignId('workout_manager_id')->after('id')->nullable()->constrained('workout_manager')->cascadeOnDelete();
            $table->foreignId('workout_libraries_id')->after('category_id')->nullable()->constrained('workout_libraries')->cascadeOnDelete();
            $table->string('unit_type')->after('weight')-> nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['workout_manager_id']);
            $table->dropColumn('workout_manager_id');
            $table->dropForeign(['workout_libraries_id']);
            $table->dropColumn('workout_libraries_id');
            $table->dropColumn('unit_type');
        });
    }
};
