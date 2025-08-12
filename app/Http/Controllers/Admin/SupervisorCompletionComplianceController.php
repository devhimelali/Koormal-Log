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
            'supervisor' => 'nullable|string|max:255'
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $startFormatted = $start->format('Y-m-d');
        $endFormatted = $end->format('Y-m-d');

        // STEP 1: Supervisor query
        $supervisorsQuery = Supervisor::whereRaw(
            "STR_TO_DATE(date, '%d-%m-%Y') BETWEEN ? AND ?",
            [$startFormatted, $endFormatted]
        );

        if ($request->filled('supervisor')) {
            $supervisorsQuery = $supervisorsQuery->where('name', 'LIKE', '%'.$request->supervisor.'%');
        }

        // Get unique supervisors by name only
        $supervisors = $supervisorsQuery
            ->select('name')
            ->distinct()
            ->pluck('name');

        if ($supervisors->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No supervisors found for the given criteria.'
            ], 404);
        }

        $reportData = [];
        $averageProgressData = [];


        foreach ($supervisors as $sup) {
            $workedDates = DB::table('supervisors')
                ->select(DB::raw("DATE_FORMAT(STR_TO_DATE(supervisors.date, '%d-%m-%Y'), '%Y-%m-%d') as parsed_date"))
                ->where('name', $sup)
                ->whereRaw("STR_TO_DATE(supervisors.date, '%d-%m-%Y') BETWEEN ? AND ?",
                    [$startFormatted, $endFormatted])
                ->pluck('parsed_date');

            $totalProgress = 0;
            $totalCompletion = 0;
            foreach ($workedDates as $date) {
                $shift = Supervisor::where('name',
                    $sup)->where(DB::raw("DATE_FORMAT(STR_TO_DATE(date, '%d-%m-%Y'), '%Y-%m-%d')"),
                    $date)->value('shift');

                $completion = HandoverCompletion::where('shift',
                    $shift->value)->where(DB::raw("DATE_FORMAT(STR_TO_DATE(log_date, '%d-%m-%Y'), '%Y-%m-%d')"), $date)
                    ->first();

                $percentage = $this->calculateYesPercentage($completion?->answers);
                $totalProgress += $percentage;
                $totalCompletion++;

                $reportData[] = [
                    'date' => $date,
                    'percentage' => $percentage,
                    'supervisor' => $sup,
                ];
            }

            $averageProgress = $totalCompletion > 0
                ? round($totalProgress / $totalCompletion, 2)
                : 0;

            $averageProgressData[$sup] = $averageProgress;
        }

        // STEP 3: Load logos
        $logo1 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/4emus.png')));
        $logo2 = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/images/koormal.png')));

        // STEP 4: Generate PDF
        $pdf = Pdf::loadView(
            $request->filled('supervisor')
                ? 'exports.supervisor_completion_compliance'
                : 'exports.supervisor_completion_compliance_all',
            [
                'supervisor' => $request->filled('supervisor') ? $reportData[0]['supervisor'] : null,
                'start_date' => $start->format('d-M-Y'),
                'end_date' => $end->format('d-M-Y'),
                'reportData' => $reportData,
                'averageProgress' => $request->filled('supervisor') ? $averageProgressData[$reportData[0]['supervisor']] : null,
                'averageProgressData' => $request->filled('supervisor') ? null : $averageProgressData,
                'logo1' => $logo1,
                'logo2' => $logo2,
            ]
        );

        $fileName = $request->filled('supervisor')
            ? 'Supervisor Completion Compliance '.time().'.pdf'
            : 'All Supervisors Completion Compliance '.time().'.pdf';

        return $pdf->download($fileName);
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
