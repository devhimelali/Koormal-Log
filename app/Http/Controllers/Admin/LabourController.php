<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LabourRequest;
use App\Models\Crew;
use App\Models\Labour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class LabourController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Labour::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('crew_name', function ($row) {
                    return $row->crew?->name;
                })
                ->addColumn('actions', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-secondary btn-sm">
                                <i class="bi bi-pencil me-2"></i>
                                Edit
                          </a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">
                                <i class="bi bi-trash me-2"></i>
                                Delete
                          </a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $crews = Crew::orderBy('name')->get();
        return view('admin.labours.index', compact('crews'));
    }

    public function store(LabourRequest $request)
    {
        Labour::create([
            'name' => $request->name,
            'crew_id' => $request->crew_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Labour added successfully'
        ]);
    }

    public function edit($id)
    {
        $labour = Labour::find($id);

        if (!$labour) {
            return response()->json([
                'status' => 'error',
                'message' => 'Labour not found.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $labour
        ]);
    }

    public function update(LabourRequest $request, $id)
    {
        $labour = Labour::find($id);

        $labour->name = $request->name;
        $labour->crew_id = $request->crew_id;
        $labour->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Labour updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $labour = Labour::find($id);

        if (!$labour) {
            return response()->json([
                'status' => 'error',
                'message' => 'Labour not found.'
            ]);
        }

        $labour->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Labour deleted successfully'
        ]);
    }
}
