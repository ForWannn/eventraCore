<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Create Divisions
        $creative = Division::firstOrCreate(['name' => 'Creative'], ['description' => 'Tim Kreatif dan Desain']);
        $ops = Division::firstOrCreate(['name' => 'Operasional'], ['description' => 'Tim Manajemen Operasional']);
        $finance = Division::firstOrCreate(['name' => 'Finance'], ['description' => 'Tim Keuangan dan Akuntansi']);
        $ae = Division::firstOrCreate(['name' => 'Account Executive'], ['description' => 'Tim Komunikasi Klien']);
        $leader = Division::firstOrCreate(['name' => 'Leader'], ['description' => 'Direksi dan Manajemen Puncak']);
        $reelSeven = Division::firstOrCreate(['name' => 'reel_seven'], ['description' => 'Divisi Utama Administrasi dan Operasional']);

        $divisionMap = [
            'Creative' => $creative->id,
            'Operasional' => $ops->id,
            'Finance' => $finance->id,
            'Account Executive' => $ae->id,
            'Leader' => $leader->id,
            'reel_seven' => $reelSeven->id,
        ];

        // 2. Seed Admin user
        $admin = User::create([
            'nik' => 'ADM-001',
            'employee_id' => 'ADM-001',
            'division_id' => $reelSeven->id,
            'name' => 'Admin Ops',
            'email' => 'admin@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 10000000,
            'phone' => '081234567890',
            'birth_date' => '1990-01-01',
            'gender' => 'Laki-laki',
            'employee_type' => 'Full Time',
            'join_date' => '2024-01-01',
        ]);
        $admin->assignRole('Admin');
        $admin->givePermissionTo(['view_dashboard']);

        // 2b. Seed Superadmin user
        $superadmin = User::create([
            'nik' => 'SAD-001',
            'employee_id' => 'SAD-001',
            'division_id' => $reelSeven->id,
            'name' => 'Superadmin',
            'email' => 'superadmin@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 12000000,
            'phone' => '081234567891',
            'birth_date' => '1988-08-08',
            'gender' => 'Laki-laki',
            'employee_type' => 'Full Time',
            'join_date' => '2024-01-01',
        ]);
        $superadmin->assignRole('Superadmin');

        // 3. User Seed Definition List (Mapped to Hikvision IDs from user image)
        $usersToSeed = [
            [
                'id_num' => '1',
                'name' => 'M. Agus Idham',
                'email' => 'agus@eventracore.com',
                'role' => 'GM',
                'division' => 'Leader',
                'salary' => 35000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '2',
                'name' => 'Bobby hendra saputra',
                'email' => 'bobby@eventracore.com',
                'role' => 'CEO',
                'division' => 'Leader',
                'salary' => 50000000,
                'gender' => 'Laki-laki',
                'join_date' => '2013-02-07',
            ],
            [
                'id_num' => '3',
                'name' => 'Andri Nugraha',
                'email' => 'andri@eventracore.com',
                'role' => 'Head',
                'division' => 'Operasional',
                'salary' => 15000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '4',
                'name' => 'Yoga Pratama',
                'email' => 'yoga@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '5',
                'name' => 'Econ',
                'email' => 'econ@eventracore.com',
                'role' => 'Head',
                'division' => 'Finance',
                'salary' => 18000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '6',
                'name' => 'Arief Khurniawan',
                'email' => 'arief@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '7',
                'name' => 'Hanifah (Nana)',
                'email' => 'nana@eventracore.com',
                'role' => 'Employee',
                'division' => 'Account Executive',
                'salary' => 8500000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '8',
                'name' => 'Siti Nuraziza Saskia',
                'email' => 'saskia@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '9',
                'name' => 'M Aditya Arbie',
                'email' => 'arbie@eventracore.com',
                'role' => 'Employee',
                'division' => 'Account Executive',
                'salary' => 8500000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '10',
                'name' => 'Ayu',
                'email' => 'ayu@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '11',
                'name' => 'Siti Tri Dita',
                'email' => 'dita@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '12',
                'name' => 'M Rifai (Fai)',
                'email' => 'fai@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '13',
                'name' => 'Reza Desten Paltama (Ejak)',
                'email' => 'ejak@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '14',
                'name' => 'Genta Prayoga',
                'email' => 'genta@eventracore.com',
                'role' => 'Head',
                'division' => 'Creative',
                'salary' => 15000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '15',
                'name' => 'Muhammad Ichwan',
                'email' => 'ichwan@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '16',
                'name' => 'Dani Pamungkas',
                'email' => 'dani@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '17',
                'name' => 'Angel Maharani Puspita',
                'email' => 'angel@eventracore.com',
                'role' => 'Head',
                'division' => 'Account Executive',
                'salary' => 16000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '18',
                'name' => 'Aldi Yusuf',
                'email' => 'aldi@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'id_num' => '19',
                'name' => 'Suci',
                'email' => 'suci@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'id_num' => '20',
                'name' => 'Relli Asmadi',
                'email' => 'relli@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
        ];

        // 4. Create Users and Assign Roles
        $createdUsers = [];
        foreach ($usersToSeed as $uData) {
            $divId = $divisionMap[$uData['division']];
            $joinDate = $uData['join_date'] ?? $faker->dateTimeBetween('-5 years', '-1 months')->format('Y-m-d');
            $birthDate = $faker->dateTimeBetween('-40 years', '-22 years')->format('Y-m-d');
            $phone = '08' . $faker->numerify('##########');

            $user = User::create([
                'nik' => $uData['id_num'],
                'employee_id' => $uData['id_num'],
                'division_id' => $divId,
                'name' => $uData['name'],
                'email' => $uData['email'],
                'password' => Hash::make('password123'),
                'base_salary' => $uData['salary'],
                'phone' => $phone,
                'birth_date' => $birthDate,
                'gender' => $uData['gender'],
                'employee_type' => 'Full Time',
                'join_date' => $joinDate,
            ]);

            $user->assignRole($uData['role']);
            $user->givePermissionTo(['view_dashboard', 'weekly_report', 'leave_request', 'attendance_history']);
            $createdUsers[$uData['id_num']] = $user;
        }

        // 5. Update direct_manager_id relationships
        // CEO (ID 2) has no direct manager (null)
        
        // GM (ID 1) reports to CEO (ID 2)
        if (isset($createdUsers['1']) && isset($createdUsers['2'])) {
            $createdUsers['1']->update(['direct_manager_id' => $createdUsers['2']->id]);
        }

        // Heads report to GM (ID 1)
        $gmId = $createdUsers['1']->id ?? null;
        if ($gmId) {
            $heads = ['5', '3', '14', '17'];
            foreach ($heads as $headIdNum) {
                if (isset($createdUsers[$headIdNum])) {
                    $createdUsers[$headIdNum]->update(['direct_manager_id' => $gmId]);
                }
            }
        }

        // Employees report to their respective Head
        $relations = [
            '5' => ['8', '10', '11', '19'],
            '3' => ['4', '6', '20'],
            '14' => ['12', '13', '15', '16', '18'],
            '17' => ['7', '9'],
        ];

        foreach ($relations as $headIdNum => $employeeIdNums) {
            $headId = $createdUsers[$headIdNum]->id ?? null;
            if ($headId) {
                foreach ($employeeIdNums as $empIdNum) {
                    if (isset($createdUsers[$empIdNum])) {
                        $createdUsers[$empIdNum]->update(['direct_manager_id' => $headId]);
                    }
                }
            }
        }

        // 6. Assign default direct permissions for backward compatibility
        if (isset($createdUsers['2'])) {
            $createdUsers['2']->givePermissionTo(['crud_events', 'rekap_absen', 'rekap_weekly', 'weekly_history', 'leave_approvals', 'crud_users']);
        }
        if (isset($createdUsers['1'])) {
            $createdUsers['1']->givePermissionTo(['rekap_absen', 'rekap_weekly', 'weekly_history', 'leave_approvals', 'crud_users']);
        }
        if (isset($createdUsers['5'])) {
            $createdUsers['5']->givePermissionTo(['rekap_weekly', 'weekly_history', 'leave_approvals', 'rekap_absen']);
        }
        $admin->givePermissionTo(['crud_users', 'manage_calendar']);
    }
}
