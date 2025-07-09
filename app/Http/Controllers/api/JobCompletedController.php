<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ShiftLog;
use Illuminate\Http\Request;

class JobCompletedController extends Controller
{
    public function markAsJobCompleted(Request $request)
    {
        $job = ShiftLog::where('asset_no', $request->asset_no)
            ->where('due_start', $request->next_due_date)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.',
            ], 404);
        }

        $job->mark_as_complete = 1;
        $job->progress = '100';
        $job->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Job marked as completed.',
        ], 200);
    }
}
