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
        Schema::create('applicant_request_reason', function (Blueprint $table) {
            $table->id();
            $table->string('txt')->nullable();
            $table->unsignedBigInteger('form')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->foreignId('applicantRequestId')->constrained('applicants_requests')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_request_reason');
    }
};
     