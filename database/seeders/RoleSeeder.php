<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
        * Run the database seeds.
        */
    public function run(): void
    {
        // Define system roles
        $roles = [
            'Direktur',
            'GM',
            'Head',
            'PIC Event',
            'Employee',
            'Intern',
            'Admin',
            'Superadmin'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Define system permissions
        $permissions = [
            'crud_users',
            'crud_events',
            'rekap_absen',
            'rekap_weekly',
            'weekly_history',
            'manage_calendar',
            'leave_approvals',
            'view_dashboard',
            'weekly_report',
            'leave_request',
            'attendance_history',
            'rekap_event'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
