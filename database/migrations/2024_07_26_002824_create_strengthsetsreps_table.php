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
        Schema::create('strengthsetsreps', function (Blueprint $table) {
            $table->id();
            $table->integer('sets')->nullable();
            $table->integer('reps')->nullable();
            $table->integer('alt_sets')->nullable();
            $table->integer('alt_reps')->nullable();
            $table->foreignId('strength_id')->constrained('strengths')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strengthsetsreps');
    }
};
