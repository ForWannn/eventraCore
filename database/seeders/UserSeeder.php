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

        // 2. Seed Admin user (not counted as active employee, not in doughnut chart)
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

        // 3. User Seed Definition List
        $usersToSeed = [
            // Leaders
            [
                'name' => 'Bobby hendra saputra',
                'email' => 'bobby@eventracore.com',
                'role' => 'CEO',
                'division' => 'Leader',
                'nik' => 'LDR-001',
                'salary' => 50000000,
                'gender' => 'Laki-laki',
                'join_date' => '2013-02-07',
            ],
            [
                'name' => 'M. Agus Idham',
                'email' => 'agus@eventracore.com',
                'role' => 'GM',
                'division' => 'Leader',
                'nik' => 'LDR-002',
                'salary' => 35000000,
                'gender' => 'Laki-laki',
            ],
            // Finance
            [
                'name' => 'Sherina Andriani',
                'email' => 'sherina@eventracore.com',
                'role' => 'Head',
                'division' => 'Finance',
                'nik' => 'FNC-001',
                'salary' => 18000000,
                'gender' => 'Perempuan',
            ],
            [
                'name' => 'Siti Tri Dita',
                'email' => 'sitidita@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'nik' => 'FNC-002',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'name' => 'Siti Nuraziza Saskia',
                'email' => 'saskia@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'nik' => 'FNC-003',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'name' => 'Suci',
                'email' => 'suci@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'nik' => 'FNC-004',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            [
                'name' => 'Ayu',
                'email' => 'ayu@eventracore.com',
                'role' => 'Employee',
                'division' => 'Finance',
                'nik' => 'FNC-005',
                'salary' => 8000000,
                'gender' => 'Perempuan',
            ],
            // Operational
            [
                'name' => 'Andri Nugraha',
                'email' => 'andri@eventracore.com',
                'role' => 'Head',
                'division' => 'Operasional',
                'nik' => 'OPR-001',
                'salary' => 15000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Yoga Pratama',
                'email' => 'yoga@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'nik' => 'OPR-002',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Aidil Septiansyah',
                'email' => 'aidil@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'nik' => 'OPR-003',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Arief Khurniawan',
                'email' => 'arief@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'nik' => 'OPR-004',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Relli Asmadi',
                'email' => 'relli@eventracore.com',
                'role' => 'Employee',
                'division' => 'Operasional',
                'nik' => 'OPR-005',
                'salary' => 7500000,
                'gender' => 'Laki-laki',
            ],
            // Creative
            [
                'name' => 'Genta Prayoga',
                'email' => 'genta@eventracore.com',
                'role' => 'Head',
                'division' => 'Creative',
                'nik' => 'CRE-001',
                'salary' => 15000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'M Rifai',
                'email' => 'rifai@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'nik' => 'CRE-002',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Aldi Yusuf',
                'email' => 'aldi@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'nik' => 'CRE-003',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Reza Desten Paltama',
                'email' => 'reza@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'nik' => 'CRE-004',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Dani Pamungkas',
                'email' => 'dani@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'nik' => 'CRE-005',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Muhammad Ichwan',
                'email' => 'ichwan@eventracore.com',
                'role' => 'Employee',
                'division' => 'Creative',
                'nik' => 'CRE-006',
                'salary' => 8000000,
                'gender' => 'Laki-laki',
            ],
            // Account Executive
            [
                'name' => 'Angel Maharani Puspita',
                'email' => 'angel@eventracore.com',
                'role' => 'Head',
                'division' => 'Account Executive',
                'nik' => 'AEX-001',
                'salary' => 16000000,
                'gender' => 'Perempuan',
            ],
            [
                'name' => 'M Aditya Arbie',
                'email' => 'arbie@eventracore.com',
                'role' => 'Employee',
                'division' => 'Account Executive',
                'nik' => 'AEX-002',
                'salary' => 8500000,
                'gender' => 'Laki-laki',
            ],
            [
                'name' => 'Hanifah',
                'email' => 'hanifah@eventracore.com',
                'role' => 'Employee',
                'division' => 'Account Executive',
                'nik' => 'AEX-003',
                'salary' => 8500000,
                'gender' => 'Perempuan',
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
                'nik' => $uData['nik'],
                'employee_id' => $uData['nik'],
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
            $createdUsers[$uData['nik']] = $user;
        }

        // 5. Update direct_manager_id relationships
        // CEO has no direct manager (null)
        
        // GM reports to CEO
        if (isset($createdUsers['LDR-002']) && isset($createdUsers['LDR-001'])) {
            $createdUsers['LDR-002']->update(['direct_manager_id' => $createdUsers['LDR-001']->id]);
        }

        // Heads report to GM
        $gmId = $createdUsers['LDR-002']->id ?? null;
        if ($gmId) {
            $heads = ['FNC-001', 'OPR-001', 'CRE-001', 'AEX-001'];
            foreach ($heads as $headNik) {
                if (isset($createdUsers[$headNik])) {
                    $createdUsers[$headNik]->update(['direct_manager_id' => $gmId]);
                }
            }
        }

        // Employees report to their respective Head
        $relations = [
            'FNC-001' => ['FNC-002', 'FNC-003', 'FNC-004', 'FNC-005'],
            'OPR-001' => ['OPR-002', 'OPR-003', 'OPR-004', 'OPR-005'],
            'CRE-001' => ['CRE-002', 'CRE-003', 'CRE-004', 'CRE-005', 'CRE-006'],
            'AEX-001' => ['AEX-002', 'AEX-003'],
        ];

        foreach ($relations as $headNik => $employeeNiks) {
            $headId = $createdUsers[$headNik]->id ?? null;
            if ($headId) {
                foreach ($employeeNiks as $empNik) {
                    if (isset($createdUsers[$empNik])) {
                        $createdUsers[$empNik]->update(['direct_manager_id' => $headId]);
                    }
                }
            }
        }
    }
}
