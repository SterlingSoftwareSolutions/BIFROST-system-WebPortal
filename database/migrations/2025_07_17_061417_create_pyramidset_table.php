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
        Schema::create('pyramidset', function (Blueprint $table) {
            $table->id();
            $table->integer('sets')->nullable();
            $table->integer('reps')->nullable();
            $table->enum('unit', ['%', 'Kg', 'Cal', '/10']); // Enum column for unit
            $table->float('pyramidweight')->nullable();
            $table->float('pyramidmale')->nullable();
            $table->float('pyramidfemale')->nullable();
            $table->foreignId('conditioning_id')->constrained('conditionings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pyramidset');
    }
};
