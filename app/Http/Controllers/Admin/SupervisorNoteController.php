<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupervisorNoteRequest;
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
        $logType = $noteType == 'day_shift' ? 'day' : 'night';
        $shiftLogs = ShiftLog::where('shift_name', $logType)
            ->where('log_date', $logDate)
            ->get();
        return view('admin.supervisor-notes.create', compact('shiftLogs', 'noteType'));
    }

    public function store(SupervisorNoteRequest $request)
    {
        $note = SupervisorNote::create([
            'shift_log_id' => $request->shift_log_id,
            'note' => $request->note,
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
            ->with('success', 'Note added successfully');
    }
}
