<?php

namespace App\Http\Controllers\Admin;

use App\Models\Labour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabourController extends Controller
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

        return response()->json(['message' => 'Labour list updated successfully.']);
    }
}
