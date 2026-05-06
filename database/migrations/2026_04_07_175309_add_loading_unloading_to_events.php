<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tambah kolom tarif global di tabel events
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('loading_fee', 15, 2)->default(0)->after('pic_fee');
            $table->decimal('unloading_fee', 15, 2)->default(0)->after('loading_fee');
        });

        // Tambah status penugasan di tabel pivot (tetap di sini agar spesifik per orang)
        Schema::table('event_position_members', function (Blueprint $table) {
            $table->boolean('is_loading')->default(false);
            $table->boolean('is_unloading')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
