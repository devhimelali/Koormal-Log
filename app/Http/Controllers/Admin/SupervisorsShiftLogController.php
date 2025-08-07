<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ShiftLogFromWorkOrderImport;
use Carbon\Carbon;
use App\Models\Labour;
use App\Models\ShiftLog;
use App\Models\Supervisor;
use App\Models\LabourShift;
use App\Models\OpportuneJob;
use Illuminate\Http\Request;
use App\Models\SupervisorNote;
use App\Imports\ShiftLogImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ShiftLogCsvExport;
use App\Models\HandoverCompletion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\ShiftLogsDataTable;
use App\Http\Requests\CopyLabourSupervisorRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;

class SupervisorsShiftLogController extends Controller
{
    public function index(ShiftLogsDataTable $dataTable, Request $request)
    {
        if ($request->has('order') && $request['order'][0]['column'] == 1) {
            $orderIndex = $request->input('order.0.column'); // column index
            $direction = $request->input('order.0.dir'); // asc or desc
            $columns = $request->input('columns');
            $columnName = $columns[$orderIndex]['data'] ?? 'unknown';
            session([
                'sorted_column' => 'shift_name',
                'sorted_direction' => $direction,
            ]);
        } else {
            session([
                'sorted_column' => 'shift',
                'sorted_direction' => 'both',
            ]);
        }

        $inputDate = $request->query('date', date('d-m-Y'));
        $day_labours = LabourShift::with('labour')
            ->where('date', $inputDate)
            ->where('shift', 'day')
            ->first();

        $night_labours = LabourShift::with('labour')
            ->where('date', $inputDate)
            ->where('shift', 'night')
            ->first();

        $supervisorQuery = Supervisor::where('date', $inputDate)->get();

        $role = $this->getUserRole();
        $isLocked = $this->isLocked($inputDate);
        $isEditable = $role === 'supervisor' && !$isLocked;

        $dayHandoverCompletion = HandoverCompletion::where('shift', 'day')
            ->where('log_date', $inputDate)
            ->first();
        $nightHandoverCompletion = HandoverCompletion::where('shift', 'night')
            ->where('log_date', $inputDate)
            ->first();
        $totalDayHandoverCompletionPercent = $this->calculateYesPercentage($dayHandoverCompletion->answers ?? []);
        $totalNightHandoverCompletionPercent = $this->calculateYesPercentage($nightHandoverCompletion->answers ?? []);


        return $dataTable->render('admin.supervisors.supervisors-shift-log', [
            'selectedDate' => $inputDate,
            'day_labours' => $day_labours,
            'night_labours' => $night_labours,
            'supervisors_day' => (clone $supervisorQuery)->where('shift', 'day')->pluck('name')->toArray(),
            'supervisors_night' => (clone $supervisorQuery)->where('shift', 'night')->pluck('name')->toArray(),
            'opportuneJobs' => OpportuneJob::get(),
            'isEditable' => $isEditable,
            'totalDayHandoverCompletionPercent' => $totalDayHandoverCompletionPercent,
            'totalNightHandoverCompletionPercent' => $totalNightHandoverCompletionPercent,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_name' => 'nullable|string',
            'wo_number' => 'nullable|string',
            'asset_no' => 'nullable|string',
            'work_description' => 'nullable|string',
            'labour' => 'nullable|string',
            'date' => 'required|date_format:d-m-Y',
        ]);

        $nextPosition = ShiftLog::max('position') + 1;
        $validated['position'] = $nextPosition;
        $validated['log_date'] = $request->date;
        $validated['shift_name'] = $request->shift_name ?? 'day';
        $validated['scheduled'] = 'no';
        $log = ShiftLog::create($validated);
        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'New Job Added Successfully',
        ]);
    }

    public function show($id)
    {
        $log = ShiftLog::find($id);
        $isLocked = $this->isLocked($log->log_date);
        return view('admin.supervisors.supervisors-shift-log-show', compact('log', 'isLocked'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);
        $allowedFields = [
            'shift_name', 'wo_number', 'asset_no', 'work_description', 'labour', 'supervisor_notes',
            'asset_description', 'duration', 'department', 'priority', 'progress', 'note', 'requisition', 'note_id',
            'scheduled'
        ];

        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $log = ShiftLog::find($id);
        if ($request->field == 'progress' && $request->value == 100) {
            $log->mark_as_complete = 1;
        } else {
            if ($request->field == 'scheduled' && $log->progress == 100) {
                $log->mark_as_complete = 1;
            } else {
                if ($log->progress != 100) {
                    $log->mark_as_complete = 0;
                }
            }
        }
        $log->{$request->field} = $request->value;
        $log->save();
        return response()->json(['success' => true, 'message' => 'Field updated successfully']);
    }

    public function updateDetails(Request $request, $id)
    {
        $shiftLog = ShiftLog::find($id);
        $shiftLog->update($request->all());
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $shiftLog->media()->create([
                    'url' => Storage::url($path),
                    'level' => 'attachment',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Details updated successfully',
            'date' => $shiftLog->log_date
        ]);
    }

    public function markComplete(Request $request, $id)
    {
        $shiftLog = ShiftLog::find($id);
        $shiftLog->update(['mark_as_complete' => !$shiftLog->mark_as_complete, 'progress' => '100']);
        if ($shiftLog->mark_as_complete == 1) {
            return redirect()->back()->with('success', 'Job marked as completed');
        } else {
            return redirect()->back()->with('success', 'Job marked as not completed');
        }
    }

    public function reorder(Request $request)
    {
        try {
            foreach ($request->order as $row) {
                ShiftLog::where('id', $row['id'])->update(['position' => $row['position']]);
            }
            return response()->json(['success' => true, 'message' => 'Shift Log Moved Successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update row order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $log = ShiftLog::find($id);
        if (!$log) {
            return response()->json(['error' => 'Job not found.'], 404);
        }

        // Delete all related media/images
        foreach ($log->media as $media) {
            $path = str_replace('/storage/', '', $media->url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $media->delete();
        }

        $log->delete();

        return response()->json(['success' => true]);
    }

    public function importShiftLog(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx',
            'log_date' => 'required|date',
            'import_type' => 'required|in:scheduler,workorder'
        ]);

        // Extract headings from the uploaded Excel file
        $headingsArray = (new HeadingRowImport)->toArray($request->file('csv_file'));
        $headers = $headingsArray[0][0] ?? [];

        // Define expected headers for each type
        $expectedScheduler = [
            'wo_no', 'description', 'duration', 'asset_no', 'trades',
            'due_start', 'work_order_status_description', 'raised',
            'start_date', 'priority', 'job_type', 'department',
            'materials_cost', 'labour_cost', 'other_cost', 'asset_description'
        ];

        $expectedWorkorder = [
            'printed', 'wo_no', 'asset_no', 'description', 'asset_description',
            'status', 'due_start', 'due_finish', 'job_type', 'reference_no',
            'priority', 'raised', 'start_date', 'finished_date', 'duration',
            'account_code', 'department', 'parent_asset', 'material_costs',
            'other_costs', 'policy_number', 'group_number', 'document_count',
            'permits_outstanding', 'risk_type', 'hazard_description',
            'risk_score', 'risk_rating'
        ];

        // Select the expected format
        $expected = $request->import_type === 'scheduler'
            ? $expectedScheduler
            : $expectedWorkorder;

        // Check if any expected headers are missing
        $missing = array_diff($expected, $headers);

        if (!empty($missing)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded file does not match the expected format for '.ucfirst($request->import_type).'.',
                'missing_columns' => array_values($missing)
            ], 422);
        }

        // Run appropriate import
        if ($request->import_type === 'scheduler') {
            Excel::import(new ShiftLogImport($request->log_date), $request->file('csv_file'));
        } else {
            Excel::import(new ShiftLogFromWorkOrderImport($request->log_date), $request->file('csv_file'));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shift logs imported successfully.'
        ], 201);
    }

    public function export(Request $request)
    {
        $request->validate([
            'shift' => 'nullable|string',
            'date' => 'required|date',
            'export' => 'required|in:csv,xlsx,pdf',
        ]);

        $supervisorQuery = Supervisor::where('date', $request->date)->get();

        if ($request->export == 'pdf') {
            $query = ShiftLog::with('note')->where('log_date', $request->date)->orderBy('wo_number', 'asc');

            if ($request->shift !== null && $request->shift != 'both') {
                $query->where('shift_name', $request->shift);
            }

            $supervisorDayShiftNotes = SupervisorNote::with('media')->where('log_date',
                $request->date)->where('note_type', 'day_shift')->first();
            $supervisorNightShiftNotes = SupervisorNote::with('media')->where('log_date',
                $request->date)->where('note_type', 'night_shift')->first();
            $day_labours = LabourShift::where('date', $request->date)
                ->where('shift', 'day')
                ->first();

            $night_labours = LabourShift::with('labour')
                ->where('date', $request->date)
                ->where('shift', 'night')
                ->first();

            $handoverCompletions = HandoverCompletion::where('log_date', $request->date)
                ->whereIn('shift', ['day', 'night'])
                ->get()
                ->keyBy('shift');

            $dayHandoverCompletion = $handoverCompletions->get('day');
            $nightHandoverCompletion = $handoverCompletions->get('night');

            $supervisorDayYesPercentage = $this->calculateYesPercentage($dayHandoverCompletion->answers ?? []);
            $supervisorNightYesPercentage = $this->calculateYesPercentage($nightHandoverCompletion->answers ?? []);

            $logs = $query->get();
            $daySupervisor = (clone $supervisorQuery)->where('shift', 'day')->pluck('name')->toArray();
            $nightSupervisor = (clone $supervisorQuery)->where('shift', 'night')->pluck('name')->toArray();
            PDF::setOptions(['isPhpEnabled' => true]);
            if ($request->shift == 'both') {
                $dayLogs = $logs->where('shift_name', 'day');
                $nightLogs = $logs->where('shift_name', 'night');

                $dayLogPercentage = $this->calculateScheduledYesPercentage($dayLogs->toArray());
                $nightLogPercentage = $this->calculateScheduledYesPercentage($nightLogs->toArray());

                $pdf = PDF::loadView('exports.shift-log', [
                    'dayLogs' => $dayLogs,
                    'nightLogs' => $nightLogs,
                    'dayLogPercentage' => $dayLogPercentage,
                    'nightLogPercentage' => $nightLogPercentage,
                    'shift' => 'both',
                    'date' => $request->date,
                    'dayLabour' => $day_labours,
                    'nightLabour' => $night_labours,
                    'daySupervisor' => $daySupervisor,
                    'nightSupervisor' => $nightSupervisor,
                    'supervisorDayShiftNotes' => $supervisorDayShiftNotes,
                    'supervisorNightShiftNotes' => $supervisorNightShiftNotes,
                    'supervisorDayYesPercentage' => $supervisorDayYesPercentage,
                    'supervisorNightYesPercentage' => $supervisorNightYesPercentage,

                ])->setPaper('a4', 'portrait');
            } else {
                $supervisorNotes = null;
                if ($request->shift == 'day') {
                    $supervisorNotes = $supervisorDayShiftNotes;
                } elseif ($request->shift == 'night') {
                    $supervisorNotes = $supervisorNightShiftNotes;
                }
                $logPercentage = $this->calculateScheduledYesPercentage($logs->toArray());
                $pdf = PDF::loadView('exports.shift-log', [
                    'logs' => $logs,
                    'logPercentage' => $this->calculateScheduledYesPercentage($logs->toArray()),
                    'shift' => $request->shift,
                    'date' => $request->date,
                    'dayLabour' => $day_labours,
                    'nightLabour' => $night_labours,
                    'daySupervisor' => $daySupervisor,
                    'nightSupervisor' => $nightSupervisor,
                    'supervisorNotes' => $supervisorNotes,
                    'supervisorDayYesPercentage' => $supervisorDayYesPercentage,
                    'supervisorNightYesPercentage' => $supervisorNightYesPercentage,
                ])->setPaper('a4', 'portrait');
            }

            return $pdf->stream('shift_log_'.$request->date.'.pdf');
        }


        if ($request->export == 'xlsx' || $request->export == 'csv') {
            $ext = $request->export;
            $fileName = 'shift_logs_'.date('Y-m-d').'.'.$ext;
            return Excel::download(new ShiftLogCsvExport($request->date, $request->shift), $fileName);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'log_date' => 'required',
        ]);
        $logDate = $request->log_date;
        $logs = ShiftLog::where('log_date', $logDate)->get();

        if ($logs->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No work order found for the provided date.',
            ], 404);
        }

        ShiftLog::where('log_date', $logDate)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Work Order deleted successfully for date: '.$logDate,
        ]);
    }

    public function storeShiftLogFromOpportuneJobs(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|in:day,night',
            'job_id' => 'required|exists:opportune_jobs,id',
            'log_date' => 'required|date_format:d-m-Y',
        ]);


        $job = OpportuneJob::find($request->job_id);

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Opportune job not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();
            ShiftLog::create([
                'shift_name' => $request->shift_name,
                'wo_number' => $job->wo_number,
                'asset_no' => $job->asset_no,
                'asset_description' => $job->asset_description,
                'work_description' => $job->work_description,
                'status' => $job->status,
                'due_start' => $job->due_start,
                'job_type' => $job->job_type,
                'priority' => $job->priority,
                'raised' => $job->raised,
                'start_date' => $job->start_date,
                'duration' => $job->duration,
                'department' => $job->department,
                'material_cost' => $job->material_cost,
                'other_cost' => $job->other_cost,
                'position' => ShiftLog::max('position') + 1,
                'log_date' => $request->log_date,
            ]);

            $job->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'New Job Added Successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create shift log.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetProgress($id)
    {
        $shiftLog = ShiftLog::findOrFail($id);

        $shiftLog->progress = 0;
        $shiftLog->save();

        return redirect()->back()->with('success', 'Progress reset successfully');
    }

    public function resetJobProgress($id)
    {
        $shiftLog = ShiftLog::findOrFail($id);

        $shiftLog->progress = 0;
        $shiftLog->mark_as_complete = 0;
        $shiftLog->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Progress reset successfully',
        ]);
    }

    public function copyToNextDays(CopyLabourSupervisorRequest $request)
    {
        $validated = $request->validated();

        switch ($validated['copy_for']) {
            case 'supervisor':
                $this->copyAssignments($validated, 'supervisor');
                break;
            case 'labour':
                $this->copyAssignments($validated, 'labour');
                break;
            case 'both':
                $this->copyAssignments($validated, 'both');
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Assignments copied successfully',
        ]);
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


    private function copyAssignments(array $validated, string $type): void
    {
        $startDate = Carbon::createFromFormat('d-m-Y', $validated['copy_days_date']);
        $howMany = (int) $validated['how_many'];

        $copyFormDate = $validated['copy_form_date'];


        for ($i = 0; $i < $howMany; $i++) {
            $targetDate = $startDate->copy()->addDays($i)->format('d-m-Y');
            if ($type === 'supervisor' || $type === 'both') {
                $this->createOrMergeName(Supervisor::class, $copyFormDate, $validated['shift'], $targetDate);
            }

            if ($type === 'labour' || $type === 'both') {
                $this->createOrMergeName(LabourShift::class, $copyFormDate, $validated['shift'], $targetDate);
            }
        }
    }

    private function createOrMergeName(string $modelClass, string $copyFormDate, string $shift, string $date): void
    {
        $shifts = $shift === 'both' ? ['day', 'night'] : [$shift];

        foreach ($shifts as $currentShift) {
            $copyData = $modelClass::where('shift', $currentShift)
                ->where('date', $copyFormDate)
                ->first();

            if ($copyData) {
                $modelClass::updateOrCreate(
                    ['date' => $date, 'shift' => $currentShift],
                    ['name' => $copyData->name]
                );
            }
        }
    }

    private function getUserRole()
    {
        return Auth::user()->roles->pluck('name')->first();
    }

    private function isLocked($log_date)
    {
        return $isLocked = now()->greaterThan(Carbon::parse($log_date)->addDay()->setTime(6, 0));
    }

    private function calculateScheduledYesPercentage(?array $jobs): float
    {
        if (empty($jobs)) {
            return 0.0;
        }

        // Filter jobs where 'scheduled' is 'yes'
        $filteredJobs = array_filter($jobs, function ($job) {
            return isset($job['scheduled'], $job['progress']) && strtolower($job['scheduled']) === 'yes';
        });

        $total = count($filteredJobs);

        if ($total === 0) {
            return 0.0;
        }

        // Sum the 'progress' values
        $totalProgress = array_reduce($filteredJobs, function ($carry, $job) {
            return $carry + (float) $job['progress'];
        }, 0.0);

        // Calculate percentage
        $percentage = ($totalProgress / $total);

        return round($percentage, 2);
    }

    public function updateCriticalWork($id)
    {
        $log = ShiftLog::find($id);

        if (!$log) {
            return response()->json(['error' => 'Job not found.'], 404);
        }

        $log->update(['critical_work' => !$log->critical_work]);

        return response()->json(['status' => 'success', 'message' => 'Updated successfully']);
    }
}
