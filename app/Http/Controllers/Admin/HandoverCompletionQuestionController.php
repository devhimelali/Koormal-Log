<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HandoverCompletionQuestionRequest;
use App\Models\HandoverCompletion;
use App\Models\HandoverCompletionQuestion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HandoverCompletionQuestionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = HandoverCompletionQuestion::query();
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
        return view('admin.handover-completion-questions.index');
    }

    public function store(HandoverCompletionQuestionRequest $request)
    {
        HandoverCompletionQuestion::create([
            'question' => $request->question,
            'status' => 1
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Question added successfully.'
        ]);
    }

    public function edit($id)
    {
        $question = HandoverCompletionQuestion::find($id);

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    public function update(HandoverCompletionQuestionRequest $request, $id)
    {
        $question = HandoverCompletionQuestion::find($id);
        $question->question = $request->question;
        $question->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Question updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $question = HandoverCompletionQuestion::find($id);
        $question->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Question deleted successfully.'
        ]);
    }
}
