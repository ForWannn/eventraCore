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

            WorkCalendar::updateOrCreate(
                ['date' => $dateStr],
                [
                    'is_working_day' => $isWorking,
                    'description' => $desc
                ]
            );
        }

        return redirect()->back()->with('success', 'Kalender kerja berhasil diperbarui.');
    }
}
