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
        
        // Add weight column to weightlifting_sets
        Schema::table('weightlifting_sets', function (Blueprint $table) {
            $table->string('weight')->nullable(); // Stores 80%, 20kg etc.
        });
        // Add weight column to strengthsetsreps
        Schema::table('strengthsetsreps', function (Blueprint $table) {
            $table->string('weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('weightlifting_sets', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
        Schema::table('strengthsetsreps', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
