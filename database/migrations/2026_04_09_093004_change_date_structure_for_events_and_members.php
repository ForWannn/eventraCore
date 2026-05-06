<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
            $table->json('event_dates')->nullable()->after('description');
            $table->time('start_time')->nullable()->after('event_dates');
            $table->time('end_time')->nullable()->after('start_time');
        });

        Schema::table('event_position_members', function (Blueprint $table) {
            $table->dropColumn(['work_start', 'work_end']);
            $table->json('work_dates')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_dates', 'start_time', 'end_time']);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
        });

        Schema::table('event_position_members', function (Blueprint $table) {
            $table->dropColumn('work_dates');
            $table->date('work_start')->nullable();
            $table->date('work_end')->nullable();
        });
    }
};
