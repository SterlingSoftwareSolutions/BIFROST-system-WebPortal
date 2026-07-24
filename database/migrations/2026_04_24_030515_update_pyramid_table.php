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
        Schema::table('pyramid', function (Blueprint $table) {

            // $table->string('restred')->nullable();
            // $table->string('restyellow')->nullable();
            // $table->string('restgreen')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pyramid', function (Blueprint $table) {

            $table->dropColumn([
                'restred',
                'restyellow',
                'restgreen'
            ]);

        });
    }
};
