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
        Schema::table('daily_attendances', function (Blueprint $table) {
            // Menambahkan kolom manual_status setelah kolom status
            $table->string('manual_status')->nullable()->after('status');
            // Menambahkan kolom admin_note (tipe text agar bisa panjang) setelah manual_status
            $table->text('admin_note')->nullable()->after('manual_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_attendances', function (Blueprint $table) {
            $table->dropColumn(['manual_status', 'admin_note']);
        });
    }
};