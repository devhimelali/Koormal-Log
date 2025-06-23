<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CrewRequest;
use App\Http\Requests\LoadCrewRequest;
use App\Models\Crew;
use App\Models\Labour;
use App\Models\LabourShift;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CrewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Crew::query();
            return DataTables::of($data)
                ->addIndexColumn()
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
        return view('admin.crews.index');
    }

    public function store(CrewRequest $request)
    {
        Crew::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Crew added successfully'
        ]);
    }

    public function edit($id)
    {
        $crew = Crew::find($id);
        if (!$crew) {
            return response()->json([
                'status' => 'error',
                'message' => 'Crew not found.'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => $crew
        ]);
    }

    public function update(CrewRequest $request, $id)
    {
        $crew = Crew::find($id);

        $crew->name = $request->name;
        $crew->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Crew updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $crew = Crew::find($id);

        if (!$crew) {
            return response()->json([
                'status' => 'error',
                'message' => 'Crew not found.'
            ], 404);
        }

        $crew->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Crew deleted successfully'
        ]);
    }

    public function loadCrew(Request $request)
    {
        $shift = $request->shift;
        $date = $request->date;
        $crews = Crew::get();
        return view('admin.crews.load-crew-modal', compact('shift', 'date', 'crews'));
    }

    public function getLabourByCrew($id)
    {
        $labours = Labour::where('crew_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $labours
        ]);
    }

    public function storeCrew(LoadCrewRequest $request)
    {
        foreach ($request->labours as $labour) {
            LabourShift::create([
                'labour_id' => $labour,
                'shift' => $request->shift,
                'date' => $request->date
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Labour added successfully'
        ]);
    }
}
