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
        //
        Schema::table('record', function (Blueprint $table) {
            $table->integer('year')->change();
            $table->longText('desc')->nullable()->change();
            $table->date('conveningDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('record', function (Blueprint $table) {
            $table->date('year')->change();
            $table->longText('desc')->change();
            $table->dropColumn('conveningDate');
        });
    }
};
