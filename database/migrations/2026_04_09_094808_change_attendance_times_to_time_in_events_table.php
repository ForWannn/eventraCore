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
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['attendance_start', 'attendance_end']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->time('attendance_start')->nullable()->after('end_time');
            $table->time('attendance_end')->nullable()->after('attendance_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['attendance_start', 'attendance_end']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('attendance_start')->nullable();
            $table->dateTime('attendance_end')->nullable();
        });
    }
};
