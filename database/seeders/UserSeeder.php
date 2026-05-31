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
        
        $realNames = [
            'Agus', 'Aidil', 'Aldi', 'Andri', 'Angel', 'Arbie', 'Arief', 
            'Bobby', 'Dani', 'Dita', 'Genta', 'Ichwan', 'Nana', 'Reza', 
            'Rifai', 'Saskia', 'Yoga', 'Gilang', 'Rudi', 'Siti'
        ];
        $nameIndex = 0;

        // Create Divisions
        $creative = Division::create(['name' => 'Creative', 'description' => 'Tim Kreatif dan Desain']);
        $ops = Division::create(['name' => 'Operasional', 'description' => 'Tim Manajemen Operasional']);
        $finance = Division::create(['name' => 'Finance', 'description' => 'Tim Keuangan dan Akuntansi']);
        $ae = Division::create(['name' => 'Account Executive', 'description' => 'Tim Komunikasi Klien']);
        $leader = Division::create(['name' => 'Leader', 'description' => 'Direksi dan Manajemen Puncak']);
        $reelSeven = Division::create(['name' => 'reel_seven', 'description' => 'Divisi Utama Administrasi dan Operasional']);

        $ceo = User::create([
            'nik' => 'LDR-001',
            'division_id' => $leader->id,
            'name' => $realNames[$nameIndex++],
            'email' => 'ceo@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 50000000,
        ]);
        $ceo->assignRole('CEO');

        $gm = User::create([
            'nik' => 'LDR-002',
            'division_id' => $leader->id,
            'name' => $realNames[$nameIndex++],
            'email' => 'gm@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 35000000,
        ]);
        $gm->assignRole('GM');

        // Seed 1 Admin account
        $adminUser = User::create([
            'nik' => 'ADM-001',
            'division_id' => $reelSeven->id,
            'name' => 'Admin Ops',
            'email' => 'admin@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 10000000,
            'join_date' => '2024-01-01',
        ]);
        $adminUser->assignRole('Admin');

        // Helper function for mass users
        $createUser = function($nik, $divId, $role, $salary, $isPic = false) use ($faker, &$nameIndex, $realNames) {
            $user = User::create([
                'nik' => $nik,
                'division_id' => $divId,
                'name' => $realNames[$nameIndex++],
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'base_salary' => $salary,
            ]);
            $roles = [$role];
            if ($isPic) {
                $roles[] = 'PIC Event';
            }
            $user->assignRole($roles);
        };

        // 2. Creative (6 Orang: 1 Head, 5 Employee)
        $createUser('CRE-001', $creative->id, 'Head', 15000000);
        for ($i=2; $i<=6; $i++) {
            $createUser('CRE-00' . $i, $creative->id, 'Employee', 8000000, $i == 2); // 1 random PIC ready
        }

        // 3. Operasional (5 Orang: 1 Head, 4 Employee)
        $createUser('OPS-001', $ops->id, 'Head', 15000000);
        for ($i=2; $i<=5; $i++) {
            $createUser('OPS-00' . $i, $ops->id, 'Employee', 7500000, $i == 2);
        }

        // 4. Account Executive / AE (3 Orang: 1 Head, 2 Employee)
        $createUser('AE-001', $ae->id, 'Head', 16000000);
        for ($i=2; $i<=3; $i++) {
            $createUser('AE-00' . $i, $ae->id, 'Employee', 8500000);
        }

        // 5. Finance (3 Orang: 1 Head, 2 Employee)
        User::create([
            'nik' => 'FIN-001',
            'division_id' => $finance->id,
            'name' => $realNames[$nameIndex++],
            'email' => 'finance@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 18000000,
        ])->assignRole('Head');
        for ($i=2; $i<=3; $i++) {
            $createUser('FIN-00' . $i, $finance->id, 'Employee', 8000000);
        }

        // 6. Anak Magang Operasional (1 Orang: Intern)
        User::create([
            'nik' => 'MAG-001',
            'division_id' => $ops->id,
            'name' => $realNames[$nameIndex++],
            'email' => 'magang@eventracore.com',
            'password' => Hash::make('password123'),
            'base_salary' => 2500000,
        ])->assignRole('Intern');
    }
}
