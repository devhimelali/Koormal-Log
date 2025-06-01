<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Imports\ShiftLogImport;
use App\Http\Controllers\Controller;
use App\Models\ShiftLog;
use Maatwebsite\Excel\Facades\Excel;

class SupervisorsShiftLog extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'both');

        $query = ShiftLog::query();

        if ($filter === 'day') {
            $query->where('shift_name', 'Day');
        } elseif ($filter === 'night') {
            $query->where('shift_name', 'Night');
        }

        $jobs = $query->get();
        return view('admin.supervisors.supervisors-shift-log', compact('jobs', 'filter'));
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

        $allowedFields = ['shift_name', 'wo_number', 'asset_no', 'work_description', 'labour'];

        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $log = ShiftLog::find($id);
        $log->{$request->field} = $request->value;
        $log->save();
        return response()->json(['success' => true, 'message' => 'Field updated successfully']);
    }
    public function destroy($id)
    {
        $log = ShiftLog::find($id);
        if (!$log) {
            return response()->json(['error' => 'Job not found.'], 404);
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
}
