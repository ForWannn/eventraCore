<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WeeklyReport;
use Carbon\Carbon;

class WeeklyReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        $startWeek = Carbon::create(2026, 1, 1)->startOfWeek();
        // Limit up to the end of June 2026
        $endWeek = Carbon::create(2026, 6, 30)->startOfWeek();
        
        $totalCreated = 0;
        
        while ($startWeek->lte($endWeek)) {
            foreach ($users as $user) {
                // Asumsi 85% kepatuhan mengumpulkan final report
                $isCompliant = rand(1, 100) <= 85;
                
                $exists = WeeklyReport::where('user_id', $user->id)
                                      ->where('week_start_date', $startWeek->format('Y-m-d'))
                                      ->exists();
                
                if (!$exists) {
                    WeeklyReport::create([
                        'user_id' => $user->id,
                        'week_start_date' => $startWeek->format('Y-m-d'),
                        // Plan biasanya disubmit Senin atau Selasa (0-1 hari setelah startOfWeek)
                        'plan_submitted_at' => $startWeek->copy()->addDays(rand(0, 1))->setHour(rand(8, 17))->format('Y-m-d H:i:s'),
                        // Final report biasanya disubmit Jumat atau Sabtu (4-5 hari setelah startOfWeek)
                        'final_submitted_at' => $isCompliant ? $startWeek->copy()->addDays(rand(4, 5))->setHour(rand(16, 20))->format('Y-m-d H:i:s') : null,
                    ]);
                    $totalCreated++;
                }
            }
            
            $startWeek->addWeek();
        }
        
        $this->command->info("Berhasil menambahkan {$totalCreated} data Laporan Mingguan dummy untuk Jan - Jun 2026!");
    }
}
