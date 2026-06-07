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
        Schema::table('users', function (Blueprint $table) {
            // Check if foreign key exists to drop it safely
            // The foreign key is typically named users_direct_manager_id_foreign
            $table->dropForeign(['direct_manager_id']);
            $table->dropColumn('direct_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('direct_manager_id')->nullable()->constrained('users')->nullOnDelete()->after('employee_type');
        });
    }
};
