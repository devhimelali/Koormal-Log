<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrderMoveRequest;
use App\Models\ShiftLog;
use App\Models\WorkOrderMove;
use Illuminate\Http\Request;

class WorkOrderMoveController extends Controller
{
    public function store(WorkOrderMoveRequest $request)
    {
        WorkOrderMove::create([
            'wo_number' => $request->wo_number,
            'from_date' => $request->from_date,
            'from_shift' => $request->from_shift,
            'to_date' => $request->to_date,
            'to_shift' => $request->to_shift,
            'reason' => $request->reason,
        ]);

        $log = ShiftLog::find($request->shift_log_id);

        $log->update([
            'wo_number' => $request->wo_number,
            'log_date' => $request->to_date,
            'shift_name' => $request->to_shift,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Work order moved successfully'
        ]);
    }
}
