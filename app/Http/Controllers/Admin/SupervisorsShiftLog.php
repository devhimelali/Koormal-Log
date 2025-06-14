<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ShiftLogsDataTable;
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

class SupervisorsShiftLog extends Controller
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
        $laboursQuery = Labour::where('date', $inputDate)->get();
        return $dataTable->render('admin.supervisors.supervisors-shift-log', [
            'selectedDate' => $inputDate,
            'labours_day' => (clone $laboursQuery)->where('shift', 'day')->pluck('name')->toArray(),
            'labours_night' => (clone $laboursQuery)->where('shift', 'night')->pluck('name')->toArray(),
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

        $datePart = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
        $timePart = now()->format('H:i:s');
        $createdAt = Carbon::parse("$datePart $timePart");
        $log = ShiftLog::create($validated);

        $log->created_at = $createdAt;
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
        ]);

        Excel::import(new ShiftLogImport, $request->file('csv_file'));

        return back()->with('success', 'Shift log CSV imported successfully!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'shift' => 'nullable|string',
            'date' => 'required|date',
            'export' => 'required|in:csv,xlsx,pdf',
        ]);
        $laboursQuery = Labour::where('date', $request->date)->get();

        if ($request->export == 'pdf') {
            $queryDate = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $query = ShiftLog::with('note')->whereDate('created_at', $queryDate);

            if ($request->shift !== null && $request->shift != 'both') {
                $query->where('shift_name', $request->shift);
            }


            //get session for orderby
            $sortedColumn = session('sorted_column', 'No column sorted yet');
            $sortedDirection = session('sorted_direction', 'No direction set yet');
            if ($sortedDirection == 'asc') {
                $query->orderBy($sortedColumn, 'asc');
            } elseif ($sortedDirection == 'desc') {
                $query->orderBy($sortedColumn, 'desc');
            }


            $logs = $query->get();
            $dayLabour = (clone $laboursQuery)->where('shift', 'day')->pluck('name')->toArray();
            $nightLabour = (clone $laboursQuery)->where('shift', 'night')->pluck('name')->toArray();

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
