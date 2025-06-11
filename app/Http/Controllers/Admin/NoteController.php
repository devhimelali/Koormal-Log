<?php

namespace App\Http\Controllers\Admin;

use App\Models\Note;
use Illuminate\Http\Request;
use App\DataTables\NoteDataTable;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index(NoteDataTable $dataTable)
    {
        return $dataTable->render('admin.notes.index');
    }
    public function store(Request $request)
    {
        $request->validate(['note' => 'required']);
        Note::create($request->only('note'));
        return response()->json(['success' => true, 'message' => 'Note created successfully.']);
    }
    public function update(Request $request, Note $note)
    {
        $request->validate(['note' => 'required']);
        $note->update($request->only('note'));
        return response()->json(['success' => true, 'message' => 'Note updated successfully.']);
    }

    public function edit(Note $note)
    {
        return response()->json($note);
    }
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['success' => true, 'message' => 'Note deleted successfully.']);
    }
}
