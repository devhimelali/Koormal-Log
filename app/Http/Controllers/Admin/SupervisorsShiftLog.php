<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\ShiftLog;
use Illuminate\Http\Request;
use App\Imports\ShiftLogImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ShiftLogCsvExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class SupervisorsShiftLog extends Controller
{
    public function index(Request $request)
    {
        $shift = $request->query('shift', 'both');
        $inputDate = $request->query('date', date('d-m-Y'));

        // Convert d-m-Y to Y-m-d for querying
        try {
            $queryDate = Carbon::createFromFormat('d-m-Y', $inputDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $queryDate = now()->format('Y-m-d');
        }
        $query = ShiftLog::query();

        if ($shift === 'day') {
            $query->where('shift_name', 'Day');
        } elseif ($shift === 'night') {
            $query->where('shift_name', 'Night');
        }

        $query->whereDate('created_at', $queryDate);

        $jobs = $query->orderBy('position')->get();

        // Pass original input date for UI display
        return view('admin.supervisors.supervisors-shift-log', [
            'jobs' => $jobs,
            'shift' => $shift,
            'date' => $inputDate,
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
        ]);
        $nextPosition = ShiftLog::max('position') + 1;
        $validated['position'] = $nextPosition;
        $log = ShiftLog::create($validated);

        return response()->json(['id' => $log->id]);
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
        $allowedFields = ['shift_name', 'wo_number', 'asset_no', 'work_description', 'labour', 'supervisor_notes', 'asset_description', 'duration', 'department', 'priority', 'progress'];

        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }
        $log = ShiftLog::find($id);
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

        return response()->json(['success' => true, 'message' => 'Details updated successfully']);
    }

    public function markComplete(Request $request, $id)
    {
        $shiftLog = ShiftLog::find($id);
        $shiftLog->update(['mark_as_complete' => !$shiftLog->mark_as_complete]);
        return redirect()->back()->with('success', 'Shift Log Marked as Complete');
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
        ]);

        Excel::import(new ShiftLogImport, $request->file('csv_file'));

        return back()->with('success', 'Shift log CSV imported successfully!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'shift_name' => 'nullable|string',
            'date' => 'required|date',
            'export' => 'required|in:csv,xlsx,pdf',
        ]);

        if ($request->export == 'pdf') {
            $queryDate = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $query = ShiftLog::whereDate('created_at', $queryDate);

            if ($request->shift) {
                $query->where('shift_name', $request->shift);
            }

            $logs = $query->get();
            $dayLabour = 'Alex Herbertson, Bill Smith, Steven Jones, Frank Reid, Mark Thomas';
            $nightLabour = 'John Winters, Albert Cummins, Ralph Grieves, Mark Riley';

            $pdf = PDF::loadView('exports.shift-log', [
                'logs' => $logs,
                'date' => $request->date,
                'dayLabour' => $dayLabour,
                'nightLabour' => $nightLabour,
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('shift_log_' . $request->date . '.pdf');
        }


        if ($request->export == 'xlsx' || $request->export == 'csv') {
            $ext = $request->export;
            $fileName = 'shift_logs_' . date('Y-m-d') . '.' . $ext;
            return Excel::download(new ShiftLogCsvExport($request->date, $request->shift), $fileName);
        }
    }
}
