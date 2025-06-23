<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Labour;
use Illuminate\Http\Request;

class LabourShiftController extends Controller
{
    public function updateLabour(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:day,night',
            'labour' => 'required|string',
            'date' => 'required|date',
        ]);

        Labour::updateOrCreate(
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
