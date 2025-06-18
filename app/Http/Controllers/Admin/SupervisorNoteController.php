<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupervisorNoteRequest;
use App\Models\Media;
use App\Models\ShiftLog;
use App\Models\SupervisorNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupervisorNoteController extends Controller
{
    public function create(Request $request)
    {
        $noteType = $request->note_type;
        $logDate = $request->log_date;
        if (!$logDate && !$noteType) {
            return redirect()
                ->route('supervisors-shift-log.index')
                ->withErrors(['error' => 'Please select date and note type']);
        }
        $supervisor_notes = SupervisorNote::with('media')->where('log_date', $logDate)->where('note_type', $noteType)->first();
        return view('admin.supervisor-notes.create', compact('supervisor_notes', 'noteType'));
    }

    public function store(SupervisorNoteRequest $request)
    {
        $note = SupervisorNote::updateOrCreate([
            'log_date' => $request->log_date,
            'note_type' => $request->note_type,
        ], [
            'note' => $request->note,
            'log_date' => $request->log_date,
            'note_type' => $request->note_type,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('supervisor_notes', $imageName, 'public');
                $note->media()->create([
                    'url' => Storage::url($path),
                    'level' => 'note-image'
                ]);
            }
        }
        return redirect()
            ->route('supervisors-shift-log.index')
            ->with('success', 'Supervisor note added successfully');
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:media,id',
        ]);

        $image = Media::findOrFail($request->image_id);
        $path = str_replace('/storage/', '', $image->url);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        $image->delete();
        return response()->json(['status' => 'success']);
    }
}
