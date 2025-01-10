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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->date('dob');
            $table->enum('gender', ['Male', 'Female']);
            $table->integer('age');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->integer('height');
            $table->integer('weight');
            $table->decimal('bmr');
            $table->string('primary_goal');
            $table->string('subscription_level');
            $table->string('image_paths')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('members', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        // });

        Schema::dropIfExists('members');
    }
};
