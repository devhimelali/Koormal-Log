<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoadCrewRequest;
use App\Models\Labour;
use App\Models\LabourShift;
use Illuminate\Http\Request;

class LabourShiftController extends Controller
{
    public function store(LoadCrewRequest $request)
    {
        $labourNames = [];

        // Get labour names from the request
        foreach ($request->labours as $labourId) {
            $labour = Labour::find($labourId);
            if ($labour) {
                $labourNames[] = $labour->name;
            }
        }

        // Check if a LabourShift already exists
        $existing = LabourShift::where('date', $request->date)
            ->where('shift', $request->shift)
            ->first();

        // Get existing names as array
        $existingNames = $existing ? explode(', ', $existing->name) : [];

        // Merge and keep unique names
        $mergedNames = array_unique(array_merge($existingNames, $labourNames));

        // Convert to comma-separated string
        $finalNamesString = implode(', ', $mergedNames);

        // Save the merged result
        LabourShift::updateOrCreate(
            [
                'date' => $request->date,
                'shift' => $request->shift
            ],
            [
                'name' => $finalNamesString,
                'shift' => $request->shift,
                'date' => $request->date
            ]
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Labour added successfully'
        ]);
    }
    public function updateLabour(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:day,night',
            'labour' => 'required|string',
            'date' => 'required|date',
        ]);

        LabourShift::updateOrCreate(
            [
                'date' => $request->date,
                'shift' => $request->shift
            ],
            [
                'name' => $request->labour
            ]
        );

        $shift = $request->shift == 'day' ? 'Day Shift' : 'Night Shift';

        return response()->json([
            'status' => 'success',
            'message' => 'Labour ' . $shift . ' list updated successfully.'
        ]);
    }
}
