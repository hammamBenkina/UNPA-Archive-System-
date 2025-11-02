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
        Schema::create('committee', function (Blueprint $table) {
            $table->id();
            $table->integer('no');
            $table->date('yearOfEstablishment');
            $table->boolean('isCurrent')->default(0);
            $table->foreignId('createdBy')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee');
    }
};
