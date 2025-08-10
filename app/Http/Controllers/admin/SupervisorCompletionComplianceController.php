<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorCompletionComplianceController extends Controller
{
    public function index()
    {
        return view('admin.supervisor-completion-compliance.index');
    }

    public function exportPdf(Request $request)
    {
        // Validate inputs
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supervisor' => 'required|string|max:255'
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $startFormatted = $start->format('Y-m-d');
        $endFormatted = $end->format('Y-m-d');

        // Fetch data filtered by date range, supervisor, and group by shift_name
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

        // Prepare table data per date and shift
        $reportData = [];
        $shiftTotals = []; // total progress per shift
        $shiftCounts = []; // number of days with data per shift

        foreach ($data as $row) {
            $date = Carbon::parse($row->parsed_date)->format('d-M-Y');
            $shift = strtolower($row->shift_name);
            $progress = round($row->avg_progress, 2);

            $reportData[$date][$shift] = $progress;

            // Track totals per shift
            if (!isset($shiftTotals[$shift])) {
                $shiftTotals[$shift] = 0;
                $shiftCounts[$shift] = 0;
            }
            $shiftTotals[$shift] += $progress;
            $shiftCounts[$shift]++;
        }

        // Calculate average per shift
        $averageProgress = [];
        foreach ($shiftTotals as $shift => $total) {
            $averageProgress[$shift] = $shiftCounts[$shift] > 0
                ? round($total / $shiftCounts[$shift], 2)
                : 0;
        }

        $logo1 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/4emus.png')));
        $logo2 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/koormal.png')));

        // Generate PDF
        $pdf = Pdf::loadView('exports.supervisor_completion_compliance', [
            'supervisor' => $request->supervisor,
            'start_date' => $start->format('d-M-Y'),
            'end_date' => $end->format('d-M-Y'),
            'reportData' => $reportData,
            'averageProgress' => $averageProgress,
            'logo1' => $logo1,
            'logo2' => $logo2,
        ]);

        return $pdf->download('Supervisor_Completion_Compliance.pdf');
    }
}
