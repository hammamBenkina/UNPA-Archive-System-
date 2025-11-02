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
        Schema::create('record_item', function (Blueprint $table) {
            $table->id();
            $table->integer('no');
            $table->date('date');
            $table->foreignId('recordId')->constrained('record');
            $table->foreignId('applicantRequestId')->constrained('applicants_requests');
            $table->foreignId('docId')->constrained('file')->nullable();
            $table->foreignId('cardId')->constrained('file')->nullable();
            $table->foreignId('createdBy')->constrained('users')->onDelete('cascade');
            $table->text('desc');
            $table->string('status')->default('accept');
            $table->text('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_item');
    }
};
