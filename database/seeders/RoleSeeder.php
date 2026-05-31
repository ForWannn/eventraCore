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
            'CEO',
            'GM',
            'Head',
            'PIC Event',
            'Employee',
            'Intern',
            'Freelance',
            'Admin'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
