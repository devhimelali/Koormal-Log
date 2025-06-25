<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrderMoveRequest;
use App\Models\ShiftLog;
use App\Models\WorkOrderMove;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WorkOrderMoveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $workOrderMoves = WorkOrderMove::get();
            return DataTables::of($workOrderMoves)
                ->addIndexColumn()
                ->editColumn('wo_number', function ($workOrderMove) {
                    return $workOrderMove->wo_number;
                })
                ->editColumn('reason', function ($workOrderMove) {
                    return nl2br($workOrderMove->reason);
                })
                ->addColumn('from_date_to_date', function ($workOrderMove) {
                    return '<span class="badge bg-body-secondary border border-primary text-primary">' .$workOrderMove->from_date.'</span> <i class="ph ph-arrow-right text-secondary"></i> <span class="badge bg-body-secondary border border-secondary text-secondary">' . $workOrderMove->to_date . '</span>';
                })
                ->addColumn('from_shift_to_shift', function ($workOrderMove) {
                    return '<span class="badge bg-body-secondary border border-primary text-primary">' . ucfirst(strtolower($workOrderMove->from_shift)) . '</span> <i class="ph ph-arrow-right text-secondary"></i> <span class="badge bg-body-secondary border border-secondary text-secondary">' . ucfirst(strtolower($workOrderMove->to_shift));
                })
                ->rawColumns(['wo_number', 'reason', 'from_date_to_date', 'from_shift_to_shift'])
                ->make(true);
        }

        return view('admin.work-order-moves.index');
    }

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
