<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ShiftLogsDataTable;
use App\Models\HandoverCompletion;
use App\Models\OpportuneJob;
use App\Models\Supervisor;
use App\Models\SupervisorNote;
use Carbon\Carbon;
use App\Models\ShiftLog;
use Illuminate\Http\Request;
use App\Imports\ShiftLogImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ShiftLogCsvExport;
use App\Http\Controllers\Controller;
use App\Models\Labour;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

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
//        $laboursQuery = Labour::where('date', $inputDate)->get();
        $supervisorQuery = Supervisor::where('date', $inputDate)->get();
        return $dataTable->render('admin.supervisors.supervisors-shift-log', [
            'selectedDate' => $inputDate,
//            'labours_day' => (clone $laboursQuery)->where('shift', 'day')->pluck('name')->toArray(),
//            'labours_night' => (clone $laboursQuery)->where('shift', 'night')->pluck('name')->toArray(),
            'supervisors_day' => (clone $supervisorQuery)->where('shift', 'day')->pluck('name')->toArray(),
            'supervisors_night' => (clone $supervisorQuery)->where('shift', 'night')->pluck('name')->toArray(),
            'opportuneJobs' => OpportuneJob::get(),
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
        return view('admin.supervisors.supervisors-shift-log-show', compact('log'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);
        $allowedFields = ['shift_name', 'wo_number', 'asset_no', 'work_description', 'labour', 'supervisor_notes', 'asset_description', 'duration', 'department', 'priority', 'progress', 'note', 'requisition', 'note_id'];

        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $log = ShiftLog::find($id);
        if ($request->field == 'progress' && $request->value == 100) {
            $log->mark_as_complete = 1;
        } else {
            $log->mark_as_complete = 0;
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
        $createdAt = Carbon::parse($shiftLog->created_at)->format('d-m-Y');
        return response()->json([
            'success' => true,
            'message' => 'Details updated successfully',
            'date' => $createdAt
        ]);
    }

    public function markComplete(Request $request, $id)
    {
        $shiftLog = ShiftLog::find($id);
        $shiftLog->update(['mark_as_complete' => !$shiftLog->mark_as_complete]);
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
            'log_date' => 'required',
        ]);

        Excel::import(new ShiftLogImport($request->log_date), $request->file('csv_file'));

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
        $laboursQuery = Labour::where('date', $request->date)->get();
        $supervisorQuery = Supervisor::where('date', $request->date)->get();

        if ($request->export == 'pdf') {
            $query = ShiftLog::with('note')->where('log_date', $request->date)->orderBy('wo_number', 'asc');

            if ($request->shift !== null && $request->shift != 'both') {
                $query->where('shift_name', $request->shift);
            }

            $supervisorDayShiftNotes = SupervisorNote::with('media')->where('log_date', $request->date)->where('note_type', 'day_shift')->first();
            $supervisorNightShiftNotes = SupervisorNote::with('media')->where('log_date', $request->date)->where('note_type', 'night_shift')->first();


            $logs = $query->get();
            $dayLabour = (clone $laboursQuery)->where('shift', 'day')->pluck('name')->toArray();
            $nightLabour = (clone $laboursQuery)->where('shift', 'night')->pluck('name')->toArray();
            $daySupervisor = (clone $supervisorQuery)->where('shift', 'day')->pluck('name')->toArray();
            $nightSupervisor = (clone $supervisorQuery)->where('shift', 'night')->pluck('name')->toArray();
            PDF::setOptions(['isPhpEnabled' => true]);
            if ($request->shift == 'both') {
                $dayLogs = $logs->where('shift_name', 'day');
                $nightLogs = $logs->where('shift_name', 'night');

                $pdf = PDF::loadView('exports.shift-log', [
                    'dayLogs' => $dayLogs,
                    'nightLogs' => $nightLogs,
                    'shift' => 'both',
                    'date' => $request->date,
                    'dayLabour' => $dayLabour,
                    'nightLabour' => $nightLabour,
                    'daySupervisor' => $daySupervisor,
                    'nightSupervisor' => $nightSupervisor,
                    'supervisorDayShiftNotes' => $supervisorDayShiftNotes,
                    'supervisorNightShiftNotes' => $supervisorNightShiftNotes,
                ])->setPaper('a4', 'portrait');
            } else {
                $supervisorNotes = null;
                if ($request->shift == 'day') {
                    $supervisorNotes = $supervisorDayShiftNotes;
                } elseif ($request->shift == 'night') {
                    $supervisorNotes = $supervisorNightShiftNotes;
                }
                $pdf = PDF::loadView('exports.shift-log', [
                    'logs' => $logs,
                    'shift' => $request->shift,
                    'date' => $request->date,
                    'dayLabour' => $dayLabour,
                    'nightLabour' => $nightLabour,
                    'daySupervisor' => $daySupervisor,
                    'nightSupervisor' => $nightSupervisor,
                    'supervisorNotes' => $supervisorNotes,
                ])->setPaper('a4', 'portrait');
            }

            return $pdf->stream('shift_log_' . $request->date . '.pdf');
        }


        if ($request->export == 'xlsx' || $request->export == 'csv') {
            $ext = $request->export;
            $fileName = 'shift_logs_' . date('Y-m-d') . '.' . $ext;
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
            'message' => 'Work Order deleted successfully for date: ' . $logDate,
        ]);
    }

    public function storeShiftLogFromOpportuneJobs(Request $request)
    {
        $validated = $request->validate([
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

        return response()->json([
            'status' => 'success',
            'message' => 'New Job Added Successfully',
        ]);
    }
}
