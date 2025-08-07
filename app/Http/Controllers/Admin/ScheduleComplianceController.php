<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleComplianceController extends Controller
{
    public function index()
    {
        return view('admin.schedule-compliance.index');
    }
    public function dailyProgressGraph(Request $request)
    {
        $start = $request->start_date
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(29);

        $end = $request->end_date
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        // Validate manually if present
        if ($request->has(['start_date', 'end_date'])) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
        }


        $startFormatted = $start->format('Y-m-d');
        $endFormatted = $end->format('Y-m-d');

        $data = DB::table('shift_logs')
            ->select(
                DB::raw("DATE_FORMAT(STR_TO_DATE(log_date, '%d-%m-%Y'), '%Y-%m-%d') as parsed_date"),
                'shift_name',
                DB::raw('AVG(progress) as avg_progress')
            )
            ->whereRaw("STR_TO_DATE(log_date, '%d-%m-%Y') BETWEEN ? AND ?", [$startFormatted, $endFormatted])
            ->groupBy('parsed_date', 'shift_name')
            ->orderBy('parsed_date')
            ->get();

        // Reformat for frontend chart.js
        $grouped = [];

        foreach ($data as $row) {
            $date = Carbon::parse($row->parsed_date)->format('d-M');
            $grouped[$date][strtolower($row->shift_name)] = round($row->avg_progress, 1);
        }

        return response()->json($grouped);
    }
}
