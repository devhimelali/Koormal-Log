<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HandoverCompletion;
use App\Models\Supervisor;
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

        // STEP 1: Fetch supervisor actual worked dates
        $workedDates = DB::table('supervisors')
            ->select(DB::raw("DATE_FORMAT(STR_TO_DATE(supervisors.date, '%d-%m-%Y'), '%Y-%m-%d') as parsed_date"))
            ->where('name', $request->supervisor)
            ->whereRaw("STR_TO_DATE(supervisors.date, '%d-%m-%Y') BETWEEN ? AND ?", [$startFormatted, $endFormatted])
            ->pluck('parsed_date');

        // STEP 2: Fetch supervisor data
        $supervisor = Supervisor::where('name', 'LIKE', '%'.$request->supervisor.'%')
            ->whereRaw("STR_TO_DATE(date, '%d-%m-%Y') BETWEEN ? AND ?", [$startFormatted, $endFormatted])
            ->first();

        if (!$supervisor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supervisor not found.'
            ], 404);
        }

        $reportData = [];
        $totalProgress = 0;
        $totalCompletion = 0;

        foreach ($workedDates as $date) {
            $completion = HandoverCompletion::where('shift', $supervisor->shift)
                ->where(DB::raw("DATE_FORMAT(STR_TO_DATE(log_date, '%d-%m-%Y'), '%Y-%m-%d')"), $date)
                ->first();
            $percentage = $this->calculateYesPercentage($completion?->answers);
            $reportData[$date] = $percentage;
            $totalProgress += $percentage;
            $totalCompletion++;
        }

        $averageProgress = $totalCompletion > 0
            ? round($totalProgress / $totalCompletion, 2)
            : 0;

        $logo1 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/4emus.png')));
        $logo2 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/koormal.png')));

        // Generate PDF
        $pdf = Pdf::loadView('exports.supervisor_completion_compliance', [
            'supervisor' => $request->supervisor,
            'shift' => $supervisor->shift,
            'start_date' => $start->format('d-M-Y'),
            'end_date' => $end->format('d-M-Y'),
            'reportData' => $reportData,
            'averageProgress' => $averageProgress,
            'logo1' => $logo1,
            'logo2' => $logo2,
        ]);

        return $pdf->download('Supervisor_Completion_Compliance.pdf');
    }

    private function calculateYesPercentage(?array $answers): float
    {
        if (empty($answers)) {
            return 0;
        }

        $total = count($answers);
        $yesCount = count(array_filter($answers, fn ($answer) => strtolower($answer) === 'yes'));

        return number_format(($yesCount / $total) * 100, 2);
    }
}
