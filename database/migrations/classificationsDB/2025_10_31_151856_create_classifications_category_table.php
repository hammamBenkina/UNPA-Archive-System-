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
        Schema::create('classifications_category', function (Blueprint $table) {
            $table->id();
            $table->string('arName', 100)->unique();
            $table->string('enName', 100)->unique();
            $table->string('arSymbol', 20)->unique();
            $table->string('enSymbol', 20)->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classifications_category');
    }
};
