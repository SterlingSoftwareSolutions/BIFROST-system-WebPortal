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
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            $table->enum('dashboard', ['enable', 'disable'])->default('disable');
            $table->enum('access', ['enable', 'disable'])->default('disable');
            $table->enum('client_management', ['enable', 'disable'])->default('disable');
            $table->enum('workout_library', ['enable', 'disable'])->default('disable');
            $table->enum('session', ['enable', 'disable'])->default('disable');
            $table->enum('financial', ['enable', 'disable'])->default('disable');
            $table->enum('communication', ['enable', 'disable'])->default('disable');
            $table->enum('statistics', ['enable', 'disable'])->default('disable');
            $table->enum('user_dashboard', ['enable', 'disable'])->default('disable');
            $table->enum('profile', ['enable', 'disable'])->default('disable');
            $table->enum('goals', ['enable', 'disable'])->default('disable');
            $table->enum('achievements', ['enable', 'disable'])->default('disable');
            $table->enum('settings', ['enable', 'disable'])->default('disable');
            $table->enum('access_type', ['read', 'write'])->default('write');
            $table->unsignedBigInteger('user_id')->unique();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
