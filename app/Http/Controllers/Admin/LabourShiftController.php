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
        // Fetch all requested labour names in one query
        $labourNames = Labour::whereIn('id', $request->labours)->pluck('name')->toArray();

        // Find or create the LabourShift for the given date and shift
        $labourShift = LabourShift::firstOrNew([
            'date' => $request->date,
            'shift' => $request->shift,
        ]);

        // Parse existing names (if any)
        $existingNames = $labourShift->name ? explode(', ', $labourShift->name) : [];

        // Merge and deduplicate names
        $mergedNames = array_unique(array_merge($existingNames, $labourNames));

        // Save the updated names
        $labourShift->name = implode(', ', $mergedNames);
        $labourShift->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Labour added successfully',
        ]);
    }
    public function updateLabour(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:day,night',
            'labour' => 'nullable|string',
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
