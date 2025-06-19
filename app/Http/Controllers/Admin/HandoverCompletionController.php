<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HandoverCompletion;
use App\Models\HandoverCompletionQuestion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HandoverCompletionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = HandoverCompletion::where('shift', $request->shift);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supervisor_name', function ($row) {
                    return $row?->supervisor_name;
                })
                ->filterColumn('supervisor_name', function ($query, $keyword) {
                    $query->whereHas('supervisor', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-href="' . route('handover-completions.show', $row->id) . '" class="viewDetails btn btn-secondary btn-sm">
                                <i class="bi bi-eye"></i>
                                View Details
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.handover-completions.index');
    }

    public function create(Request $request)
    {
        $shift = $request->shift;
        $date = $request->date;
        $questions = HandoverCompletionQuestion::get();
        $handoverCompletion = HandoverCompletion::where('log_date', $date)->where('shift', $shift)->first();
        return view('admin.handover-completions.create', compact('questions', 'shift', 'date', 'handoverCompletion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift' => 'required|in:day,night',
            'date' => 'required|date_format:d-m-Y',
            'answers' => 'required|array',
        ]);

        HandoverCompletion::updateOrCreate([
            'log_date' => $validated['date'],
            'shift' => $validated['shift'],
        ], [
            'log_date' => $validated['date'],
            'shift' => $validated['shift'],
            'answers' => $validated['answers'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Handover Completion added successfully.'
        ]);
    }

    public function show($id)
    {
        $handoverCompletion = HandoverCompletion::find($id);
        return view('admin.handover-completions.show', compact('handoverCompletion'));
    }


}
