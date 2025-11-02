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
        Schema::create('record', function (Blueprint $table) {
            
            $table->id();
            $table->integer('no');
            $table->string('referenceNumber')->nullable();
            $table->date('year');
            $table->foreignId('branchId')->constrained('branch');
            $table->foreignId('committeeId')->constrained('committee');
            $table->foreignId('docId')->constrained('file')->nullable();
            $table->foreignId('createdBy')->constrained('users')->onDelete('cascade');
            $table->longText('desc');
            $table->timestamps();

            $table->unique(['no', 'branchId', 'committeeId', 'year',]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record');
    }
};
