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
            $table->string('phone')->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('birth_date');
            $table->string('employee_type')->default('Full Time')->after('gender');
            $table->foreignId('direct_manager_id')->nullable()->constrained('users')->nullOnDelete()->after('employee_type');
            $table->date('join_date')->nullable()->after('direct_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['direct_manager_id']);
            $table->dropColumn([
                'phone',
                'birth_date',
                'gender',
                'employee_type',
                'direct_manager_id',
                'join_date'
            ]);
        });
    }
};
