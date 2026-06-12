<?php

namespace App\Http\Controllers;

use App\Models\WorkCalendar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkCalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Fetch overrides for this month
        $overrides = WorkCalendar::whereBetween('date', [
            $startOfMonth->format('Y-m-d'),
            $endOfMonth->format('Y-m-d')
        ])->get()->keyBy(fn($item) => $item->date->format('Y-m-d'));

        // Generate all dates in this month
        $dates = [];
        $temp = $startOfMonth->copy();
        while ($temp->lte($endOfMonth)) {
            $dateStr = $temp->format('Y-m-d');
            $isWeekend = $temp->dayOfWeek === Carbon::SATURDAY || $temp->dayOfWeek === Carbon::SUNDAY;
            
            $isWorkingDay = !$isWeekend;
            $description = '';

            if (isset($overrides[$dateStr])) {
                $isWorkingDay = $overrides[$dateStr]->is_working_day;
                $description = $overrides[$dateStr]->description;
            }

            $dates[] = [
                'date' => $dateStr,
                'day_name' => $temp->locale('id')->translatedFormat('l'),
                'day_num' => $temp->format('d'),
                'is_weekend' => $isWeekend,
                'is_working_day' => $isWorkingDay,
                'description' => $description,
            ];
            $temp->addDay();
        }

        return view('settings.calendar', compact('dates', 'year', 'month'));
    }

    public function update(Request $request)
    {
        $datesData = $request->input('dates', []); // array of [date => [is_working_day, description]]

        foreach ($datesData as $dateStr => $data) {
            $isWorking = isset($data['is_working_day']) ? (bool) $data['is_working_day'] : false;
            $desc = isset($data['description']) ? trim($data['description']) : null;

            // Check if it's changing from non-working to working
            $carbon = Carbon::parse($dateStr);
            $isWeekend = $carbon->isWeekend();
            
            $existing = WorkCalendar::where('date', $dateStr)->first();
            $wasWorking = $existing ? (bool)$existing->is_working_day : !$isWeekend;

            WorkCalendar::updateOrCreate(
                ['date' => $dateStr],
                [
                    'is_working_day' => $isWorking,
                    'description' => $desc
                ]
            );

            if (!$wasWorking && $isWorking) {
                // Send WhatsApp notification
                $namaHari = $carbon->locale('id')->translatedFormat('l');
                $tanggal = $carbon->locale('id')->translatedFormat('d F Y');

                $users = \App\Models\User::whereDoesntHave('roles', function($q) {
                    $q->whereIn('name', ['Intern', 'Admin', 'Superadmin']);
                })->get();

                foreach ($users as $user) {
                    if (!empty($user->phone)) {
                        $message = "📅 [INFO KALENDER KERJA]\n\n"
                                 . "Hai {$user->name}, ada pemberitahuan penting!\n"
                                 . "Hari {$namaHari}, tanggal {$tanggal} berstatus TETAP MASUK KERJA operasional ya.\n\n"
                                 . "Jangan lupa untuk tetap melakukan absen pada tanggal tersebut. Terima kasih!";
                        \App\Services\FonnteService::send($user->phone, $message);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Kalender kerja berhasil diperbarui.');
    }
}
