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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('user_type', ['super admin', 'admin', 'client', 'worker'])->default('client');
            $table->string('email')->unique();
            $table->unsignedInteger('pin1');
            $table->unsignedInteger('pin2');
            $table->unsignedInteger('pin3');
            $table->unsignedInteger('pin4');
            $table->timestamps();
        });
               
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
